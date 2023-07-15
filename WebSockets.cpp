#include "headers.hpp"

std::vector<std::shared_ptr<boost::beast::websocket::stream<boost::asio::basic_stream_socket<boost::asio::ip::tcp>>>> WebSockets::openConnections;
void WebSockets::socketAcceptorThread()
{
    boost::asio::io_context ioContext;
    boost::asio::ip::tcp::acceptor acceptor(ioContext, {boost::asio::ip::address::from_string(DatabaseHandler::address), 9003});
    // Main loop to accept WebSocket connections
    while (true)
    {
        boost::asio::ip::tcp::socket socket(ioContext);
        acceptor.accept(socket);

        // Handle WebSocket connection in a separate thread
        std::thread wsThread(handleWebSocket, std::move(socket));
        wsThread.detach();
    }
}

// Function to handle a WebSocket connection
void WebSockets::handleWebSocket(boost::asio::ip::tcp::socket &&socket)
{
    try
    {
        std::shared_ptr<boost::beast::websocket::stream<boost::asio::ip::tcp::socket>> ws = std::make_shared<boost::beast::websocket::stream<boost::asio::ip::tcp::socket>>(std::move(socket));
        // boost::beast::websocket::stream<boost::asio::ip::tcp::socket> ws(std::move(socket));
        ws->accept();
        {
            std::lock_guard<std::mutex> socketlock(socketMutex);
            openConnections.push_back(ws); 
        }
        std::cout << "User Connected\nTotal Users: " << openConnections.size() << std::endl;
        boost::beast::multi_buffer buffer;

        // Main loop to handle WebSocket messages
        while (true)
        {
            try
            {
                // Receive and handle WebSocket messages
                ws->read(buffer);
                onMessage(*ws, buffer);
            }
            catch (const boost::beast::system_error &e)
            {
                std::lock_guard<std::mutex> lock(socketMutex);
                std::cerr << "WebSocket exception: " << e.what() << std::endl;
                for (auto i = openConnections.begin(); i != openConnections.end(); i++)
                {
                    if ((*i).get() == ws.get())
                    {
                        openConnections.erase(i);
                        std::cout << "Users Rem: " << openConnections.size() << std::endl;
                        break;
                    }
                }
                break;
            }
        }
    }
    catch (const boost::beast::system_error &e)
    {
        std::cerr << "WebSocket exception: " << e.what() << std::endl;
    }
}

// Function to handle incoming WebSocket messages
void WebSockets::onMessage(boost::beast::websocket::stream<boost::asio::ip::tcp::socket> &ws, boost::beast::multi_buffer &buffer)
{
    std::string message = boost::beast::buffers_to_string(buffer.data());

    {
        std::lock_guard<std::mutex> lock(queueMutex);
        inputQueue.push(message);
    }

    buffer.consume(buffer.size()); // Clear the buffer

    // Response
    ws.write(boost::asio::buffer("Order Placed"));

    // Notify about the new input
    queueCV.notify_one();
}

void WebSockets::broadcastThread()
{
    while (true)
    {
        std::string output;

        // Retrieve outgoing WebSocket messages
        {
            std::unique_lock<std::mutex> lock(outputMutex);

            // Wait for new output
            outputCV.wait(lock, []
                          { return !outputQueue.empty(); });

            output = std::move(outputQueue.front());
            outputQueue.pop();
        }

        {
            std::lock_guard<std::mutex> socketlock(socketMutex);
            for (auto &conn : openConnections)
            {
                // Parallel processing can be added
                try
                {
                    conn->write(boost::asio::buffer(output));
                }
                catch (const boost::beast::system_error &e)
                {
                    std::cerr << "WebSocket exception: " << e.what() << std::endl;
                }
            }
        }
    }
}

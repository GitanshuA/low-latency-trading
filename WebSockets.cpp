#include "headers.hpp"

std::vector<std::shared_ptr<boost::beast::websocket::stream<boost::asio::basic_stream_socket<boost::asio::ip::tcp>>>> WebSockets::openConnections;
void WebSockets::socketAcceptorThread()
{
    boost::asio::io_context ioContext;
    boost::asio::ip::tcp::acceptor acceptor(ioContext, {boost::asio::ip::tcp::v4(), 8080});
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
    std::shared_ptr<boost::beast::websocket::stream<boost::asio::ip::tcp::socket>> ws = std::make_shared<boost::beast::websocket::stream<boost::asio::ip::tcp::socket>>(std::move(socket));
    // boost::beast::websocket::stream<boost::asio::ip::tcp::socket> ws(std::move(socket));
    ws->accept();
    {
        std::lock_guard<std::mutex> socketlock(socketMutex);
        WebSockets::openConnections.push_back(ws);
    }
    boost::beast::multi_buffer buffer;

    // Main loop to handle WebSocket messages
    while (true)
    {
        // Receive and handle WebSocket messages
        ws->read(buffer);
        onMessage(*ws, buffer);
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
    std::string output;

    // Retrieve outgoing WebSocket messages
    {
        std::unique_lock<std::mutex> lock(outputMutex);

        // Wait for new output
        outputCV.wait(lock, []{ return !outputQueue.empty(); });

        output = std::move(outputQueue.front());
        outputQueue.pop();
    }

    {
        std::lock_guard<std::mutex> socketlock(socketMutex);
        for (auto &conn : WebSockets::openConnections)
        {
            // parallel processing can be added
            conn->write(boost::asio::buffer(output));
        }
    }
}

#include "headers.hpp"
std::queue<std::string> inputQueue;
std::mutex queueMutex, logicMutex, socketMutex;
std::condition_variable queueCV;

std::queue<std::string> outputQueue;
std::mutex outputMutex;
std::condition_variable outputCV;

//Logic Thread Not Required (Can be added if I start handling orders for different tickers seperately)
// void logicThread(std::string &&request)
// {
//     std::lock_guard<std::mutex> lock(logicMutex);
//     ProcessRequest(request);
// }


int main()
{
    DatabaseHandler::init();

    std::thread WSAcceptorThread(WebSockets::socketAcceptorThread);
    std::thread Broadcast(WebSockets::broadcastThread);

    while (true)
    {
        std::string request;
        std::unique_lock<std::mutex> lock(queueMutex);
        queueCV.wait(lock, []{ return !inputQueue.empty(); });
        request = std::move(inputQueue.front());
        inputQueue.pop();
        ProcessRequest(std::move(request));
    }

    WSAcceptorThread.join();
}
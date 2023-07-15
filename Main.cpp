#include "headers.hpp"
std::queue<std::string> inputQueue;
std::mutex queueMutex, logicMutex, socketMutex;
std::condition_variable queueCV;

std::queue<std::string> outputQueue;
std::mutex outputMutex;
std::condition_variable outputCV;

//Threads for handling main logic can be introduced if different tickers are handled separately

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
    Broadcast.join();
    WSAcceptorThread.join();
}
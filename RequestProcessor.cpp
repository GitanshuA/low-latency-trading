#include "headers.hpp"

void ProcessRequest()
{
    std::string request;
    getline(std::cin, request);
    Order new_order(request);
    std::cout<<"Started"<<std::endl;
    ProcessOrder(new_order);
    std::cout<<"Processed"<<std::endl;
}

void ProcessRequest(std::string request)
{
    Order new_order(request);
    std::thread OrderInsertThread(DatabaseHandler::CreateOrder, new_order);
    OrderInsertThread.detach();
    ProcessOrder(std::move(new_order));
    std::cout<<"Processed: "<<request<<std::endl;
}

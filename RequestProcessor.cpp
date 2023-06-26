#include "headers.hpp"

void ProcessRequest()
{
    std::string request;
    getline(std::cin, request);
    Order new_order(request);
    ProcessOrder(new_order);
    std::cout<<"Processed"<<std::endl;
}

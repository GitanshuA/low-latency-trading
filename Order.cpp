#include "headers.hpp"

Order::Order(std::string request)
    { // This blob can be moved to request processor if required
        std::string token;
        std::istringstream iss(request);
        iss >> token;
        id = stoi(token);
        iss >> token;
        userid = stoi(token);
        iss >> token;
        type = token[0];
        iss >> token;
        stock = token; // This can be removed
        iss >> token;
        price = stoi(token);
        iss >> token;
        quantity = stoi(token);
        price_fetched = 0;
        sqlMutex = new std::mutex;
    }
#include "headers.hpp"

class OrderBooks{
    public:
        static std::map<std::string, OrderBooks>  BookManager;
    public:                             //Access Modifier to be changed
        std::set<Limit> SellingTree;
        std::set<Limit> BuyingTree; 
};
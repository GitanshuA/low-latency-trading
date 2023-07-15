#include "headers.hpp"

std::unordered_map<std::string, OrderBooks> OrderBooks::BookManager;
// std::unordered_map<int, Order> OrderBooks::OrderTracker;
void ProcessOrder(Order order)
{
    if(order.type=='B')
    {
        auto itr = OrderBooks::BookManager[order.stock].BuyingTree.find(Limit(order.price));
        
        if(itr !=OrderBooks::BookManager[order.stock].BuyingTree.end())
        {
            Limit& limit = const_cast<Limit&>(*itr);
            limit.list.emplace_back(order);
            
        }
        else
        {
            if(!MatchingEngine::MatchWithSell(order))
            OrderBooks::BookManager[order.stock].BuyingTree.insert(Limit(order.price, order));
            else;
        }
    }
    else if(order.type=='S')
    {
        auto itr = OrderBooks::BookManager[order.stock].SellingTree.find(Limit(order.price));
        if(itr != OrderBooks::BookManager[order.stock].SellingTree.end())
        {
            Limit& limit = const_cast<Limit&>(*itr);
            limit.list.emplace_back(order);
        }
        else
        {
            if(!MatchingEngine::MatchWithBuy(order))
            OrderBooks::BookManager[order.stock].SellingTree.insert(Limit(order.price, order));
            else;
        }
    }
}
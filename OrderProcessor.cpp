#include "headers.hpp"

std::map<std::string, OrderBooks> OrderBooks::BookManager;
void ProcessOrder(Order order)
{
    if(order.type=='B')
    {
        //Yahan par error ho sakta hai if the value is not in map
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
    else
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
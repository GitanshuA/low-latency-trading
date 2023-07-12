#include "headers.hpp"

bool MatchingEngine::MatchWithSell(Order &order)
{
    int fulfill, quant = 0;
    bool completed{false};
    for (auto itr = OrderBooks::BookManager[order.stock].SellingTree.begin(); itr != OrderBooks::BookManager[order.stock].SellingTree.end();)
    {
        Limit &limit = const_cast<Limit &>(*itr); // Something fishy about this (If causes error, use pointer)
        if (order.quantity < 1 || limit.value > order.price)
            break;
        else
        {
            std::cout<<"flag"<<std::endl;
            while (!empty(limit.list))
            {
                fulfill = std::min(limit.list.front().quantity, order.quantity);
                order.quantity -= fulfill;
                limit.list.front().quantity -= fulfill;

                if (limit.list.front().quantity == 0)
                {
                    // add code for existing order completion [UPDATE STATUS]
                    limit.list.pop_front(); // As list is indexed we can use (total-removed) mech for deleting orders
                }

                // update existing order quantity
                if (order.quantity == 0)
                {
                    // add code for what do when a new order gets fulfilled [UPDATE STATUS]
                    completed = true;
                    return completed;
                }
            }
            itr = OrderBooks::BookManager[order.stock].SellingTree.erase(itr);
        }
    }
    // Update new order quantity in database
    return completed;
}

bool MatchingEngine::MatchWithBuy(Order &order)
{
    int fulfill;
    bool completed{false};
    std::set<Limit>::reverse_iterator itr;
    for (itr = OrderBooks::BookManager[order.stock].BuyingTree.rbegin(); itr != OrderBooks::BookManager[order.stock].BuyingTree.rend();)
    {
        Limit &limit = const_cast<Limit &>(*itr);
        if (order.quantity < 1 || limit.value < order.price)
            break;
        else
        {
            while (!empty(limit.list))
            {
                fulfill = std::min(limit.list.front().quantity, order.quantity);
                order.quantity -= fulfill;
                limit.list.front().quantity -= fulfill;
                if (limit.list.front().quantity == 0)
                {
                    // add code for existing order completion [UPDATE STATUS]
                    limit.list.pop_front(); // As list is indexed we can use (total-removed) mech for deleting orders
                }
                // update existing order quantity
                if (order.quantity == 0)
                {
                    // add code for what to do when a new order gets fulfilled [UPDATE STATUS]
                    std::cout << "Order " << order.id << " sold for " << limit.value << "\n";
                    completed = true;
                    return completed;
                }
            }
            itr++;
            OrderBooks::BookManager[order.stock].BuyingTree.erase(itr.base());
        }
    }
    // Update new order quantity in database
    return completed;
}
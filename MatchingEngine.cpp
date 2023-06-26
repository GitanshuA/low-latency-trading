#include "headers.hpp"

bool MatchingEngine::MatchWithSell(Order &order)
    {
        int fulfill;
        for (auto &itr : OrderBooks::BookManager[order.stock].SellingTree)
        {
            Limit &limit = const_cast<Limit &>(itr);
            if (order.quantity < 1 || limit.value > order.price)
                break;
            else
            {
                for (size_t i = 0; i < limit.list.size(); i++)
                {
                    fulfill = std::min(limit.list[i].quantity, order.quantity);
                    order.quantity -= fulfill;
                    limit.list[i].quantity -= fulfill;
                    
                    // update existing order quantity
                    if (order.quantity == 0)
                    {
                        // add code for what do when a new order gets fulfilled [UPDATE STATUS]
                        std::cout<<"Order "<<order.id<< " completed"<<"\n";
                        return true;
                    }
                    if (limit.list[i].quantity == 0)
                    {
                        // add code for existing order completion [UPDATE STATUS]
                        limit.list.erase(limit.list.begin() + i); // As list is indexed we can use (total-removed) mech for deleting orders
                    }
                }
            }
        }
        // Update new order quantity in database
        return false;
    }

bool MatchingEngine::MatchWithBuy(Order &order)
{
        int fulfill;
        for (auto &itr : boost::adaptors::reverse(OrderBooks::BookManager[order.stock].BuyingTree))
        {
            Limit &limit = const_cast<Limit &>(itr);
            if (order.quantity < 1 || limit.value < order.price)
                break;
            else
            {
                for (size_t i = 0; i < limit.list.size(); i++)
                {
                    fulfill = std::min(limit.list[i].quantity, order.quantity);
                    order.quantity -= fulfill;
                    limit.list[i].quantity -= fulfill;
                    // update existing order quantity
                    if (order.quantity == 0)
                    {
                        // add code for what do when a new order gets fulfilled [UPDATE STATUS]
                        std::cout<<"Order "<<order.id<< " sold for "<<limit.value<<"\n";
                        return true;
                    }
                    if (limit.list[i].quantity == 0)
                    {
                        // add code for existing order completion [UPDATE STATUS]
                        limit.list.erase(limit.list.begin() + i); // As list is indexed we can use (total-removed) mech for deleting orders
                    }
                }
            }
        }
        // Update new order quantity in database
        return false;
    }
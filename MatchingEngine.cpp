#include "headers.hpp"

bool MatchingEngine::MatchWithSell(Order &order)
{
    int fulfill;
    bool completed{false};
    for (auto itr = OrderBooks::BookManager[order.stock].SellingTree.begin(); itr != OrderBooks::BookManager[order.stock].SellingTree.end();)
    {
        Limit &limit = const_cast<Limit &>(*itr);
        if (order.quantity < 1 || limit.value > order.price)
            break;
        else
        {
            while (!empty(limit.list))
            {
                Order& cur_front = limit.list.front();
                fulfill = std::min(cur_front.quantity, order.quantity);
                order.quantity -= fulfill;
                cur_front.quantity -= fulfill;
                cur_front.price_fetched+=(fulfill*order.price);

                //Update Matched Order Status [UPDATE STATUS]
                std::thread UpdateThread(DatabaseHandler::UpdateOrderS,cur_front.id, cur_front.init_quantity-cur_front.quantity, cur_front.price_fetched, cur_front.sqlMutex);
                UpdateThread.detach();

                if (cur_front.quantity == 0)
                {
                    limit.list.pop_front();
                }

                // update existing order quantity
                if (order.quantity == 0)
                {
                    // add code for what do when a new order gets fulfilled [UPDATE STATUS]
                    completed = true;
                    std::thread UpdateThread(DatabaseHandler::UpdateOrderB,order.id, order.init_quantity-order.quantity, order.sqlMutex);
                    UpdateThread.detach();
                    return completed;
                }
            }
            itr = OrderBooks::BookManager[order.stock].SellingTree.erase(itr);
        }
    }
    // Update new order quantity in database
    std::thread UpdateThread(DatabaseHandler::UpdateOrderB,order.id, order.init_quantity-order.quantity, order.sqlMutex);
    UpdateThread.detach();
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
                Order& cur_front = limit.list.front();
                fulfill = std::min(cur_front.quantity, order.quantity);
                order.quantity -= fulfill;
                cur_front.quantity -= fulfill;
                order.price_fetched+=(fulfill*limit.value);

                //Update Matched Order Status [UPDATE STATUS]
                std::thread UpdateThread(DatabaseHandler::UpdateOrderB,cur_front.id, cur_front.init_quantity-cur_front.quantity, cur_front.sqlMutex);
                UpdateThread.detach();

                if (cur_front.quantity == 0)
                {
                    limit.list.pop_front();
                }
                // update existing order quantity
                if (order.quantity == 0)
                {
                    // add code for what to do when a new order gets fulfilled [UPDATE STATUS]
                    completed = true;
                    std::thread UpdateThread(DatabaseHandler::UpdateOrderS,order.id, order.init_quantity-order.quantity, order.price_fetched, order.sqlMutex);
                    UpdateThread.detach();
                    return completed;
                }
            }
            OrderBooks::BookManager[order.stock].BuyingTree.erase(--(itr.base()));
            itr = OrderBooks::BookManager[order.stock].BuyingTree.rbegin();
        }
    }
    // Update new order quantity in database
    std::thread UpdateThread(DatabaseHandler::UpdateOrderS,order.id, order.init_quantity-order.quantity, order.price_fetched, order.sqlMutex);
    UpdateThread.detach();
    return completed;
}
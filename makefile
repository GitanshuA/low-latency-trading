backend: Main.o Limit.o Order.o MatchingEngine.o DatabaseHandler.o WebSockets.o RequestProcessor.o OrderProcessor.o headers.hpp
	g++ -o backend Main.o Limit.o Order.o MatchingEngine.o DatabaseHandler.o WebSockets.o RequestProcessor.o OrderProcessor.o headers.hpp -lmysqlcppconn
	rm *.o

Main.o:
	g++ -c Main.cpp

Limit.o:
	g++ -c Limit.cpp

Order.o:
	g++ -c Order.cpp

MatchingEngine.o:
	g++ -c MatchingEngine.cpp

DatabaseHandler.o:
	g++ -c DatabaseHandler.cpp

WebSockets.o:
	g++ -c WebSockets.cpp

RequestProcessor.o:
	g++ -c RequestProcessor.cpp

OrderProcessor.o:
	g++ -c OrderProcessor.cpp

clean:
	rm *.o backend

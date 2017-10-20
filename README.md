# Qua**Troop**
> Qua**Troop** is a dynamic user hierarchy system which can link any selected user to 'n' number of managers and 'n' number of team members.

It's main features include the following:
 + There is no limit to the value of 'n'.
 + Any existing user in the system can be assigned as a manager or a team member to the current user selected.
 + The user hierarchy management requires a flexible structure to store a list of “People reporting to User” and a list of “User reports to People”.
 + For every user one can select a list of users (existing in system) for both fields: “People reporting to User” & “User reports to People”.

![QuaTroop](/screenshots/screencapture-localhost-8000-1479766681182.png)

##Installation Instructions

### Linux 

1. Using git, clone this repository to your local file system or download the zip:
```
git clone https://github.com/prabhakar267/quatroop.git
```

2. Make sure to have composer installed in order to install all required dependencies:
```
https://getcomposer.org/download/
```
3. a. If you installed composer locally, cd into the project directory, then run:
```
php composer.phar install
```
b. If you installed composer globally, cd into the project directory, then run:
```
composer install
```
4. During the composer install you will be prompted to provide connection details to the database server. 
5. Run the following command which will create the database using the details provided during the composer install.
```
php bin/composer doctrine:database:create
```
6. Now we need to build the database schema. Run the following command:
```
php bin/composer doctrine:schema:create
```

Now we need to setup a web server

#### PHP Local Test Server
To get a quick web server up and running for local development purposes we can go through the following easy steps:

1. Make sure you cd into the project directory then run the following command:
```
php bin/console server:run 
```

This should now allow you, by default, to go to http://127.0.0.1:8000/ and use the application. Ideally however, you would set the application up using a production web server such as Apache or Nginx, as the provided PHP web server is meant purely for local development and testing purposes and is in no way ready for production use.

## Available Routes
+ ### /user
This gives a list of all the users in the system and once we select anyone out of them, it gives information about all that specific selected user. It gives the following information about the user:
  + All users who the current user reports to (All Levels)
  + All users reporting to current user (All Levels)
  + All users who the current user reports to (One Level)
  + All users reporting to current user (One Level)

+ ### /add-user
This gives a panel to add a new user in the system. We can select the immediate parent of the new user and accordingly the changes are done automatically in the system giving a new User ID to the user.

+ ### /edit-user
This gives a panel to add new connections for an existing user in the system. We can select the a new parent and a new child and accordingly the changes are done automatically in the system.

## Algorithm Used
**Breadth First Search (BFS)** algorithm traverses a graph in a breadthward motion and uses a queue to remember to get the next vertex to start a search, when a dead end occurs in any iteration.

<img src="https://upload.wikimedia.org/wikipedia/commons/4/46/Animated_BFS.gif">

```
Breadth-First-Search(Graph, root):

for each node n in Graph:            
    n.distance = INFINITY        
    n.parent = NIL

create empty queue Q      

root.distance = 0
Q.enqueue(root)                      

while Q is not empty:        
    current = Q.dequeue()
    for each node n that is adjacent to current:
        if n.distance == INFINITY:
            n.distance = current.distance + 1
            n.parent = current
            Q.enqueue(n)
```

## Dependencies
Qua**Troop** is built over Symfony 3 PHP Framework using MySQL Database. All the dependencies are listed below:
 + [Symfony 3](http://symfony.com/)
 + [Bootstrap 3](http://getbootstrap.com/)
 + [Admin LTE](https://github.com/almasaeed2010/AdminLTE)
 + [jQuery 1.11+](http://jquery.com/)

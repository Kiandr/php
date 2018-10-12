# Seeding data
[Phinx](http://docs.phinx.org/en/latest/seeding.html) is used to allow seeding your database with test data.

> Seed classes are a great way to easily fill your database with data after it’s created. 

Our seed classes are stored in the [`database/seeds`](../database/seeds) directory.

### Demo Seeder
Need demo data to test with? There's a script for that:

```bash
$ php tools/phinx.php seed:run -s DemoSeeder
```

The [DemoSeeder](../database/seeds/DemoSeeder.php) is very simple. It just performs the MySQL queries in [`database/seeds/demo/data_crm.sql`](../database/seeds/demo/data_crm.sql). 
 
This file contains the data used to demo and test the REW CRM: [`database/seeds/demo/data_crm.sql`](../database/seeds/demo/data_crm.sql)   

### How to run a seeder class

```bash
$ php tools/phinx.php seed:run -s <SeederName>
```

### Available seeder class

- [BlogSeeder](../database/seeds/BlogSeeder.php)
  - [x] 5 main categories
  - [x]  N(0-3) sub-categories
  - [x] 5 blog links
  - [x] 20 blog tags
  - [x] 15 blog entries
  - [x] N(0-5) comments
  
- [CRMSeeder](../database/seeds/CRMSeeder.php)
  - [x] 25 groups
  - [x] 10 campaigns
  - [x] 1,000 leads
  - [ ] action plans
  - [ ] form letters
  - [ ] uploader files
  - [ ] auto-responders
  - [ ] calendar events
  - [ ] social connect
  
- [CompanySeeder](../database/seeds/CompanySeeder.php)
  - [x] 10 teams
  - [x] 100 offices
  - [x] 100 lenders
  - [x] 100 associates
  - [x] 1,000 agents
  
- [ContentSeeder](../database/seeds/ContentSeeder.php)
  - [x] 10 snippets
  - [x] 50 pages
  - [x] 10 links
  - [x] 10 testimonials
  - [ ] 10 communities
  
- [DashboardSeeder](../database/seeds/DashboardSeeder.php)
    * 1 new lead sign up every 1 hour for last 3 days
    * 1 new inquiry every 2 hours for last 3 days
    * 1 new message every 3 hours for last 3 days

### Full disclosure:
 - Data is fake. Thanks [`Faker`](https://github.com/fzaninotto/Faker).
 - Prepare to be Lorem ipsumed.
 - Multiple executions ~~might~~ **will** throw duplicate key error.
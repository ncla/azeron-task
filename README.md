# Requirements
  * PHP 7.2
  * mysql or maria db
  * laravel ^6.0


# TASK

## Create Crud endpoints, for CalendarController (add, edit, delete, list)

## Basic Structure
### app/Models (month, day, year, calendar)
#### app/Model relations:
    - calendar: (calendar->year)
    - year: (calendar<-year->month)
    - month: (year<-month->day)
    - day: (month<-day)

### tests/Feature/calendarTest.php (add, edit, delete, list)
    - autorization() // this will check if autorization works correctly
    - add()
    - edit()
    - delete()
    - list()

### CalendarController should contain crud endpoints
    - add() CustomRequest needs to Receive year, day month fields who all are validated.
    - edit() CustomRequest needs to Receive year, day month and calendar record ID who all are validated
    - delete() CustomRequest needs to Receive Calendar record ID
    - list() CustomRequest needs to Receive and be validated
        - filter :
            -order_by
                 * this can only accept ASC or DESC
                 default: order by Year Month Day DESC 
                 custom: order by any field or fields in DB (month, day, year, calendar) and ignore all others
            -in
                 * there can be not in but in, and, or fields
                 default: none
                 custom: in should accept any field or fields in DB (month, day, year, calendar) and ignore all others

    * All data given in endpoints listed should be in raw json format
    * All endpoints should use CustomRequests (app/Http/Requests) and validate incoming data, check for authorize 
    * Endpoints add, edit, delete should allow only user with role admin to be able to use them
    * Endpoints list should allow only user with role admin, user to be able to use them
      

### List endpoint raw json input.
```
{
  "order_by": {
      "user_id": "DESC/ASC"
  },
  "filter": {
      "in" :{
        "id": 1
      }
  }
}
```

### Bonus points for
    - Creating documentation with Swagger API and publishing it under (api/documentation)
    - Creating git repository and pushing it onto web
    - Dockeraizing this project

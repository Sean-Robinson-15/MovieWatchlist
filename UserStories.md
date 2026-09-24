
# User Stories


## Epic-01 : Account Management


### US-01 : Create an Account
  

**As a** new visitor, **I want** to create an account using my email address and a password **so that** I can maintain my personal watchlist.

**Acceptance Criteria**

- [ ] The user can access a registration page
- [ ] The user must provide a valid email address
- [ ] The passowrd must contain atleast 8 characters
- [ ] The email must be stored in a normalised lowercase format
- [ ] The password must be securely hashed before storage
- [ ] The email adddress must be unique
- [ ] After successful registration, the user is automatically logged in and taken to their watchlist
- [ ] Invalid input produces an understandable error message

### US-02 : Log into account

**As a** registered user, **I want** to log in to my account, **so that** I can access my personal Watchlist

**Acceptance Criteria**

- [ ] The user can submit their email address and password
- [ ] Valid credentials create an authenticated session
- [ ] The session stores the users identity and email address
- [ ] The user is redirected to their watchlist after successful login
- [ ] Invalid credentials do not create a session
- [ ] The user receives a generic error message when auth fails

### US-03 : Log Out

**As an** authenticated user, **I want** to log out of my account, **so that** my watchlist cannot be accessed from the current session.

**Acceptance Criteria**

- [ ] The user can log out using a POST request
- [ ] The current session data is cleared
- [ ] The session is destroyed
- [ ] The user is redirected home
- [ ] Protected pages require authentication again after logout

## Epic-02 : Movie Discovery

### US-04 : Search for movies

**As an** authenticated user, **I want** to search for movies by title, **so that** I can find films to add to my watchlist.

**Accpetance Criteria**

- [ ] The search page is only accessible to authenticated users
- [ ] The user can enter a movie title or search term
- [ ] The application sends the search request to the TMDB API
- [ ] The results display movie titles, release years, and overviews
- [ ] Each result displays the movie title
- [ ] Each result displays the year of release
- [ ] Each result displays the overview
- [ ] Each results has an option to add it to the watchlist
- [ ] An empty search does not trigger an external API requets
- [ ] An appropriate message is displays when no results are found
- [ ] An appropriate error message is displayed in TMDB is unavailable

### US-05 : Add a movie to my watchlist

**As an** authenticated user, **I want** to add a movie to my watchlist, **so that** I can keep track of films I intend to watch.

**Acceptance criteria**

- [ ] Only authenticated users can add films
- [ ] The request must contain a valid CSRF token
- [ ] The movie is stored useing its TMDB ID
- [ ] The movie is not duplicated in the movie database if it already exists
- [ ] The movie is associated with the authenticated user
- [ ] The item initially receives the `planned`status
- [ ] The user is redirected to their watchlist after adding the movie
- [ ] The same movie cannot be added more than once by the same user

### US-06 : View my watchlist

**As an** authenticated user, **I want** to view my saved movies, **so that** I can manage the films I want to watch.

**Acceptance Criteria**

- [ ] The watchlist page is protected from unautheticated access
- [ ] Only the current users watchlist items are displyed
- [ ] Each item displays the movie title and release year
- [ ] Each item displays its current status, ratuing and notes
- [ ] Items are ordered by their most recent update
- [ ] An empty watchlist provides a link to search for movies

### US-07 : Filter my watchlist

**As an** authenticate user, **I want** to filer my watchlist by viewing status, **so that** I can foucs on planned, currently watched or completed films.

**Acceptance Criteria**

- [ ] The user can view all watchlist items
- [ ] The user can filter by `planned`
- [ ] The user can filter by `watching`
- [ ] The user can filter by `watched`
- [ ] Filtering only affects the current users items
- [ ] Invalid filter values do not expose other data or cause an error

### US-08 : Update a watchlist item

**As an** authenciated user, **I want** to update a movies status, rating and notes, **so that** my watchlist relfects my progress and opinions.

**Acceptance Criteria**

- [ ] The user can set the status to `planned`, `watching` or `wanted`
- [ ] The user can optionally assign a rating from one to five
- [ ] The user can remove a rating by selecting "No Rating"
- [ ] The user can add or edit personal notes.
- [ ] Invalid statuses are rejected
- [ ] Ratings outside the rande of one to five are rejected
- [ ] Notes are trimmed before storage
- [ ] The items updated timestamp changes after a successful update
- [ ] A user cannot update another users watchlist item
- [ ] A calid CSRF toke is required

### US-09 : Remove a movie from my watchlist

**As an** authenticaed user, **I want** to remove a movie from my watchlist, **so that** I can keep my list accurate.

**Acceptance Criteria**
- [ ] The user can remove an item from their wathclist
- [ ] A valiad CSRF token is required
- [ ] The item is deleted only if it belongs to the user
- [ ] Removing an item does not remove the shared movie record
- [ ] The user is redirected back to their watchlist

## Epic-03 - Group functionality User Stories

### US-10 : Create a group for shared watchlist

**as an** authenticated user, **I want** to create a group and give it a name, **so that** I can organise film nights or shared viewing plans with other users.

**Acceptance Criteria**

- [ ] The user can create a group
- [ ] A group name must be between 2 and 80 characters
- [ ] A unique six-character join code is generated automatically
- [ ] The user becomes the owner of the new group
- [ ] The owner is added to the group membership list immediately
- [ ] The user is redirected to the group details page after creation
- [ ] Invalid names are rejected with a clear validation message

### US-11 : Join a group using the unique six digit code

**as an** authenticated user, **I want** to join a group by entering a valid join code, **so that** I can participate in a groups movie decisions

**Acceptance Criteria**

- [ ] The user can enter a group join code from the groups page
- [ ] The code is validated against existing groups
- [ ] The code is matched regardless of case
- [ ] If the code is valid, the user is added as a group member
- [ ] If the code is invalid, the user receives a helpful error message
- [ ] Duplicate membership is prevented
- [ ] The user is redirected to the group page after joining

### US-12 : View group membership and shared movie activity

**as a** group member, **I want** to view my groups details and shared movie activity, **so that** I can understand which films are being considered by the group.

**Acceptance Criteria**

- [ ] Group members can access a dedicated group page
- [ ] The group page shows the group name and current member count
- [ ] The page displays a shareable join code to owners and group members
- [ ] The page lists the group members and their role
- [ ] Members can view aggregated movie data for the group
- [ ] Movies can be filtered by status
- [ ] Only members of the group can access its page
- [ ] Non-members are blocked and shown an access error


### US-13 : Leave a group

**As a** group member, **I want** to leave a group when I no longer want to participate, **so that** my membership is kept accurate

**Acceptance Criteria**
 - [ ] A member can leave a group from the groups page
 - [ ] Owners cannot leave until the group is transferred or deleted
 - [ ] A member is removed from the group membership list after leaving
 - [ ] The user is redirected back to the groups overview page
 - [ ] A user cannot leave a group that are not a member of

### US-14 : Delete a group (Owner Only)

**As the** owner of a group, **I want** to delete the group, **so that** I can remove the group when it is no longer needed.

**Acceptance Criteria** 

 - [ ] Only the owner can delete the group
 - [ ] The owner must confirm the action before deletion
 - [ ] The group is removed from the system after successful deletion
 - [ ] All associated group membership data is removed as part of the group lifecycle
 - [ ] Non-owners cannot delete the grou
 - [ ] The user is redirected back the groups page after successful deletion

### US-14 : Vote on a film in the group

**As a** group member, **I want** to vote for a movie from the groups shared watchlist, **so that** the group can decide what to watch next.

**Acceptance Criteria** 

 - [ ] The group, has a dedicated "Watch Next" page
 - [ ] The page lists films shared by the group members
 - [ ] Each film displays the number of votes received
 - [ ] A user can vote for a film once
 - [ ] Voting can be toggled; reselecting the same film removes the vote
 - [ ] The user can only vote for movies that belong to the groups shared pool
 - [ ] The users current selection is clearly visible
 - [ ] A non-member cannot access or submit a vote

## Epic-04 - Non-Functional User Stories

### US-15 : Usable error handling

**as a** user, **I want** clear feedback when an operation fails, **so that** I understand what went wrong and how to continue

**Acceptance Criteria**
- [ ] Validation errors are shown after failed registration or login
- [ ] External movie-search failures display a user-friendly message
- [ ] Invalid watchlist statuses and ratings display validation feedback
- [ ] Errors do no expose passwords, API keys, SQL statements or any other internal implementation details

### US-16 : Maintainable system structure

**As a** developer, **I want** routing, business logic, data access and infrastructure code to be separated, **so that** individual features can be modified and tested independently.

**Acceptance Criteria**

- [ ] Route dispatching is handled by `Router`
- [ ] Business rules are handled by service classes
- [ ] SQL queries are handled by repository classes
- [ ] External TMDB communication is handled by `TmdbClient`
- [ ] Views are rendered through the presentation layer
- [ ] Configuration and Database connection logic remain separate from business logic

### US-17 : Secure User Data

**as a** user, **I want** my personal account and watchlist data to be protected, **so that** only I can access and manage my information.

**Acceptance Criteria**
 - [ ] Passwords are securely hashed before being stored
 - [ ] Users can only access their own watchlist data
 - [ ] Protected pages require authentication
 - [ ] State changing forms require a valid CSRF token
 - [ ] Invalid or unauthorised requests are rejected without exposing sensitive data

### US-18 : Application Availability 

**As a** user, **I want** the application to remain available when my external movie services are temporarily unavailable, **so that** I can still access and manage my existing watchlist

**Acceptance Criteria**

 - [ ] TMDB search failures display a user-friendly error message
 - [ ] The application does not expose technical error details
 - [ ] Users can still access their existing watchlist when movie searching is unavailable
 - [ ] Existing watchlist data remains accessible and unchanged.
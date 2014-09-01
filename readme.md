mushroom-project
================

API required for mushroom project


### Response Formats

All success responses are in JSON format, every error responses are in HTTP Response, so when speaking to API, please check HTTP Response first, then check JSON, if exists


### Response Codes

* **200:** The request was successful.
* **201:** The resource was successfully created.
* **204:** The request was successful, but we did not send any content back.
* **400:** The request failed due to an application error, such as a validation error.
* **401:** An API key was either not sent or invalid.
* **403:** The resource does not belong to the authenticated user and is forbidden.
* **404:** The resource was not found.
* **500:** A server error occurred.


### API Specifications

* any connection is STATELESS, no cookies, so each authenticated POST/GET should be using HTTP AUTH, except **user_reg**
* Example URL is http://picture-foods.com/api/public/v1/recipe_search/keyword (HTTP-AUTH)
* User available "test", password "test"
* IN API call, there is no difference between admin, and other users
* when using facebook login, after mobile application authenticate using facebook please call user_facebook_reg
* when calling API using facebook login please use username : facebook_email_address, and password : facebook_identifier, except user_login


### API Base URL

* API base URL is http://picture-foods.com/api/public/


## API Endpoints

### [-] GET /v1/home_background_image

Retrieve an image URL set by ADMIN from WORDPRESS. This will be displayed as Home Screen Background Image

### [D] POST /v1/user_reg

Create a new user. Returns status code **201** on success. Accepts the following parameters:

* **username** &ndash; The username of user.
* **user_email** &ndash; The email address of user.
* **password** &ndash; The password of user.
* **first_name** &ndash; Firstname of user.
* **last_name** &ndash; Lastname of user.
* **website** &ndash; Website of user.
* **photo** &ndash; Photo file of user.

There is also a few validation :
- username : no duplicate, required
- user_email : no duplicate, required
- password : required
- website : when passed to API call, it will check if it's a valid URL, if not Error will be returned

Just to make it clear, the login process will check if username is in email address format, if it's in email address format, it will reject that username input.


### [D] POST /v1/user_facebook_reg

Create a new user based from facebook user profile. Returns status code **201** on success. When there is already same fbIdentifier and email in database, it will return that data instead creating new entry. Accepts the following parameters:

* **fbIdentifier** &ndash; Facebook identifier (Facebook ID, consist only number).
* **email** &ndash; Facebook email.
* **displayName** &ndash; Facebook display name.
* **firstName** &ndash; Facebook first name.
* **lastName** &ndash; Facebook last name.
* **photoURL** &ndash; Facebook photo URL.

There is also a few validation :
- fbIdentifier : no duplicate, required
- email : no duplicate, required


### [D] POST /v1/user_login

Login. Returns status code **200** on success and JSON of user profile array. Only use this method to authenticate, FYI below parameters is HTTP-AUTH. Accepts the following parameters:

* **username** &ndash; The username of user. If using facebook login, use facebook email address as username
* **password** &ndash; The password of user. If using facebook login, use facebook identifier as password

### [-] POST /v1/user_forgot_password

Forgot Password. Returns status code **200** on success and JSON of user profile array. Accepts the following parameter:

* **login** &ndash; The username/email of user.

This Forgot Password feature will behave like this, when /user_forgot_password called, will check if the login used is email address format or not, if address format, the it will search in email address column, if not it will find in username column, if data is found, it will send email to registered user email address, and please click link in that email, to reset new password, and the API will create new generated password.


### [-] POST /v1/user_profile_edit

Update current  user profile, the parameter is from user_reg/user_facebook_reg.


### [D] POST /v1/picture_add

* **picture** &ndash; Recipe Picture (File).
* **title** &ndash; Recipe Title (Varchar).
* **description** &ndash; Recipe Description (Tiny Text).

### [D] GET /v1/picture_list

Return all image from user uploaded picture (this is user image gallery)


### [D] GET /v1/picture_list_all

Return all image from all user uploaded picture (this is all user image gallery)


### [D] POST /v1/picture_edit/{id}

* **title** &ndash; Recipe Title (Varchar).
* **description** &ndash; Recipe Description (Tiny Text).

picture_edit won't allow to edit/replace picture file, to do so, you should use picture_delete, and then picture_add.


### [D] DELETE /v1/picture_delete/{id}

Delete the recipe with the given ID. Returns status code **204** on success.


### [D] POST /v1/recipe_add

Create a new recipe data. Returns status code **201** on success. Accept the following parameters:

* **title** &ndash; Recipe Title (varchar 255)
* **recipe** &ndash; Recipe Detail (text).
* **main_picture_id** &ndash; Picture ID (Integer).
* **steps** &ndash; Steps (JSON format).
* **ingredients** &ndash; Ingredients (JSON format).

Example :
title : Recipe4
recipe : Detail of Recipe4
steps : [{"sort": 1,"stepImageID": "123213123","stepDetails": "this is step 1, this is step 1 . this is step 1"},{"sort": 2,"stepImageID": "12552343", "stepDetails": "this is step 2, this is step 2 . this is step 2"}]
ingredients : [{"sort": 1,"ingCount": 2,"ingDescription": "two tea spoon salt"},{"sort": 2,"ingCount": 4,"ingDescription": "five cup flour"}]

JSON should be in correct format.

### [D] POST /v1/recipe_edit/{id}

Update the recipe data with the given ID. Accept parameters:

* **title** &ndash; Recipe Title (varchar 255)
* **recipe** &ndash; Recipe Detail (text).
* **main_picture_id** &ndash; Picture ID (Integer).



### [D] DELETE /v1/recipe_delete/{id}

Delete the recipe with the given ID. Returns status code **204** on success.


### [D] GET /v1/recipe_list

Retrieve an array of the recipes from logged in user. Using paging feature. 


### [D] GET /v1/recipe_count

Retrieve counted recipe from logged in user.


### [D] GET /v1/recipe_list_user/{id}

Retrieve an array of the recipes from user with {id}. Using paging feature.


### [D] GET /v1/recipe_list_all

Retrieve an array of the recipes from all users except current user. Using paging feature.


### [D] GET /v1/recipe_search/{keyword}

Retrieve an array of the recipe from current logged in user. keyword is string. Will search in "Title".


### [D] GET /v1/recipe_search_all/{keyword}

Retrieve an array of the recipe from all user. keyword is string. Will search in "Title".


### [D] GET /v1/recipe_like_status/{id}

Retrieve an array of the like status for recipe ID.
Example return value 
{
	count: 1 // this is like_count + dislike_count
	current_user_like: "L" // current logged in user status, 'L'=Like, 'D'=Dislike, '-'=no status
	like_count: 1 // number of user like this recipe id
	dislike_count: 0 // number of user dislike this recipe id
}

### [D] GET /v1/like_list

Retrieve an array of the all like from current user, return with Recipe JSON format


### [D] GET /v1/like_list_all

Retrieve an array of the all like from all user, return with Recipe JSON format


### [D] GET /v1/like_list_user/{$id}

Retrieve an array of the all like from user ID, return with Recipe JSON format


### [D] POST /v1/recipe_like

* **recipe_id** &ndash; Recipe ID (bigint)

Like recipe with given recipe ID. FYI, the logged in user cannot like and dislike for the same recipe ID.


### [D] POST /v1/recipe_unlike

* **recipe_id** &ndash; Recipe ID (bigint)

Revoke like recipe with given recipe ID


### [D] POST /v1/recipe_dislike

* **recipe_id** &ndash; Recipe ID (bigint)

Unlike recipe with given recipe ID


### [D] POST /v1/recipe_undislike

* **recipe_id** &ndash; Recipe ID (bigint)

Revoke unlike recipe with given recipe ID


### [D] POST /v1/bookmark_add

Create a new bookmark data.  Returns status code **201** on success. Accept the following parameters:

* **recipe_id** &ndash; Recipe ID (bigint)


### [D] GET /v1/bookmark_list

Retrieve an array of the recipes bookmarked from logged in user.


### [D] GET /v1/bookmark_count

Retrieve counted bookmark from logged in user.


### [D] GET /v1/bookmark_list_all

Retrieve an array of the recipes bookmarked from all user.


### [D] GET /v1/bookmark_list_user/{id}

Retrieve an array of the recipes bookmarked from user with {id}.


### [D] DELETE /v1/bookmark_delete/{id}

Delete the bookmark with the given ID. Returns status code **204** on success.


## **DEVELOPMENT API**

Below APIs available only when under development process. 
[D] GET /v1/admin_truncate_bookmark   Truncate table Bookmark
[D] GET /v1/admin_truncate_picture    Truncate table Picture and all file uploads
[D] GET /v1/admin_truncate_recipe     Truncate table Recipe


Legend 
--------------------------------------------------
* [D] = Done Implemented
* [W] = Work in progress / Incomplete implementation
* [B] = Bug in API Call Implementation
* [-] = Unimplemented API Call


## **Feel free to comment any of these API endpoint.**

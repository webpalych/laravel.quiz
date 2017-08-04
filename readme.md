# Laravel Quiz Application API

# 1.Routes

## 1.1.Admin routes

##### POST | admin/auth - Admin authentication
'name' => 'required',\
'password' => 'required',

##### GET|HEAD | admin/questions - List of all questions in the admin panel

##### POST | admin/questions - Store the question with answers
'question_text' => 'required | unique:questions',\
'language_id' => 'required | exists:languages,id',\
'answers' => 'array | between:4,4',\
'answers.[].answer_text' => 'required',\
'answers.[].is_right' => 'required | boolean',

##### GET|HEAD | admin/questions/{questions} - get question by ID

##### PUT|PATCH | admin/questions/{questions} - update question by ID
'question_text' => 'required | unique:questions,question_text', (current ID ingnored)\
'language_id' => 'required | exists:languages,id',\
'answers' => 'array | between:4,4',\
'answers..id' => 'sometimes | numeric',\
'answers..answer_text' => 'required',\
'answers..is_right' => 'required | boolean',

##### DELETE | admin/questions/{questions} - delete question by ID

##### GET|HEAD  | admin/questions/language/{lang_id} - get questions by language

##### GET|HEAD  | admin/languages  - List of all languages

##### POST      | admin/languages  - Store new language
'name' => 'required | unique:languages',\
'slug' => 'required | unique:languages'
     
##### PUT|PATCH | admin/languages/{languages}  - Update language by ID
'name' => 'required | unique:languages',\
'slug' => 'required | unique:languages'

##### GET|HEAD  | admin/languages/{languages} - Get language by ID

##### DELETE    | admin/languages/{languages} - Delete language by ID

##### GET|HEAD  | admin/public_rooms  - Returns array of active and public rooms

## 1.2.Players routes

##### POST  | auth - authentication OR registration a new user
'name' => 'required',\
'email' => 'required | email | unique:users',

## 1.3.Room routes

##### POST | room/create - create new room
'is_public' => 'required | boolean'

##### GET|HEAD | room/join/{id} - join to room by ID (exists socket event)

##### GET|HEAD | room/leave/{id} - leave room by ID (Not used, but suddenly you need)

##### GET|HEAD | room/isAdmin/{id} - is user admin of the room

##### GET|HEAD | room/public_rooms - get not started public rooms

## 1.4.Quiz Process routes

##### POST | quiz/start_quiz - start quiz by room ID (exists socket event)
user should be the room admin\
'room' => 'required | numeric',\
'stepsCount' => 'required | numeric',\
'lang' => 'required | numeric', 

##### POST | quiz/start_private_quiz - start quiz by room ID and user's quiz (exists socket event)
user should be the room admin\
'room' => 'required | numeric',\
'stepsCount' => 'required | numeric',\
'quiz_id' => 'required | numeric', 

##### GET|HEAD | quiz/get_players/{roomID} - get all room players by ID

##### POST | quiz/check_results - Send answer to check (exists socket event).
'room' => 'required | numeric',\
'step' => 'required | numeric',\
'question' => 'required | numeric',\
'answer' => 'required | numeric',\
'time' => 'required | numeric', 

##### POST | quiz/check_private_results - Send answer to check (exists socket event).
'room' => 'required | numeric',\
'step' => 'required | numeric',\
'question' => 'required | numeric',\
'answer' => 'required | numeric',\
'time' => 'required | numeric', 

## 1.5.Private Questions routes

##### POST | private/questions/{quiz_id} - store new private question
'question_text' => 'required',\
'answers' => 'array | between:4,4',\
'answers.[].answer_text' => 'required',\
'answers.[].is_right' => 'required | boolean',

##### PUT|PATCH | private/questions/{quiz_id}/{question_id} - update private question
'question_text' => 'required',\
'answers' => 'array | between:4,4',\
'answers.[].id' => 'sometimes | numeric',\
'answers.[].answer_text' => 'required',\
'answers.[].is_right' => 'required | boolean',

##### DELETE | private/questions/{quiz_id}/{question_id}  - delete private question

##### GET|HEAD | private/questions/{quiz_id}/{question_id}  - get private question

## 1.6.Private Quizzes routes

##### GET|HEAD | private/quizzes - get all user's quizzes

##### POST | private/quizzes - store new user's quiz 
'quiz_name' => 'required',

##### GET|HEAD | private/quizzes/{quizzes} - get one private quiz with questions
  
##### PUT|PATCH | private/quizzes/{quizzes} - update user's quiz
'quiz_name' => 'required',

##### DELETE | private/quizzes/{quizzes} -  delete user's quiz

# 2.Socket Events

## Emits to subscribe

##### RoomChanges - room changes
'data' => [\
      'user' => Username,\
      'type' => Leave or Join,\
]

##### SendQuestion - send question from server
'data' => [\
       'question' => Question Text, //  string\
       'answers' => Answers Array\
]

##### SendIntermediateResults - send intermediate results from server
'data' => [\
       'results' => Results Array\
]

##### PlayerAnswered - fire when player answer the question
'data' => [\
        'player' => Player Name\
]

## Emits to send

##### startQuiz - start quiz
var data = {\
         'room' : id,\
         'stepsCount': number,\
         'lang': id,\
         'user' : token (without 'Bearer')\
};

##### startPrivateQuiz - start private quiz
var data = {\
         'room' : id,\
         'stepsCount': number,\
         'quiz_id': id,\
         'user' : token (without 'Bearer')\
};

##### sendResult - send answer to check
 var data = {\
          'room' : id,\
          'user' : token ,(without 'Bearer')\
          'step' : number,\
          'question' : id,\
          'answer' : id,\
          'time' : milliseconds
};

##### sendPrivateResult - send answer to check in private quiz
 var data = {\
          'room' : id,\
          'user' : token ,(without 'Bearer')\
          'step' : number,\
          'question' : id,\
          'answer' : id,\
          'time' : milliseconds
};

##### joinRoom - join to room
var data = {\
         'room' : id,\
         'user' : token (without 'Bearer')\
};
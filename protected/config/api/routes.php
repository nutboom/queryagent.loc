<?php
  return array(
/***
 * Users - Респонденты
 */

      # Возвращает captcha
        array('respondent/api/captcha', 'pattern'=>'/api/v1/captcha', 'verb'=>'GET'),

      # Авторизация клиентов
        array('respondent/api/autorizeUsers', 'pattern'=>'api/v1/authorize', 'verb'=>'GET'),

      # Выход авторизованного пользователя из системы
        array('respondent/api/logoutUsers', 'pattern'=>'api/v1/logout', 'verb'=>'GET'),

      # Отправка идентификационных данных при регистрации
        array('respondent/api/createUsers', 'pattern'=>'api/v1/users/register', 'verb'=>'POST'),

      # Предоставляет авторизованному пользователю возможность получать Push уведомления
        array('respondent/api/addPushNotificationID', 'pattern'=>'api/v1/users/profile/device', 'verb'=>'POST'),

      # Получение профиля пользователя
        array('respondent/api/viewUsers', 'pattern'=>'api/v1/users/profile', 'verb'=>'GET'),

      # Подтверждение идентификационных данных
        array('respondent/api/confirmParam', 'pattern'=>'api/v1/users/profile/confirm', 'verb'=>'GET'),

      # Повторный запрос кода подтверждения данных
        array('respondent/api/codeParam', 'pattern'=>'api/v1/users/profile/code', 'verb'=>'GET'),

      # Получение баланса
        array('respondent/api/balanceUsers', 'pattern'=>'api/v1/users/profile/balance', 'verb'=>'GET'),

      # Смена пароля
        array('respondent/api/changePasswordUsers', 'pattern'=>'api/v1/users/profile/changepassword', 'verb'=>'POST'),

      # Восстановление пароля
        array('respondent/api/newPasswordUsers', 'pattern'=>'api/v1/users/profile/newpassword', 'verb'=>'POST'),

      # Вывод средств
        array('respondent/api/paymetUsers', 'pattern'=>'api/v1/users/profile/pay', 'verb'=>'POST'),

      # Сохранение изображения пользователя - аватарки
        array('respondent/api/createUsersAvatar', 'pattern'=>'api/v1/users/profile/avatar', 'verb'=>'POST'),

      # Сохранение профиля пользователя
        array('respondent/api/updateUsers', 'pattern'=>'api/v1/users/profile', 'verb'=>'POST'),

/***
 * Справочники
 */

      // Получение справочников
        array('respondent/api/listDictionaries', 'pattern'=>'api/v1/dictionaries', 'verb'=>'GET'),

/***
 * Quiz - Опросы
 */

      # Получение количественной информации об опросах
        array('respondent/api/totalListQuiz', 'pattern'=>'api/v1/quiz/total', 'verb'=>'GET'),

      # Получение списка опросов
        array('respondent/api/listQuiz', 'pattern'=>'api/v1/quiz', 'verb'=>'GET'),

      # Получение идентификатора опроса по хэшу 
        array('respondent/api/getIdByHash', 'pattern'=>'api/v1/quizid/<hash:\w+>/omi_aud_id/<omi_aud_id:\d+>', 'verb'=>'GET'),//

      # Получение информации об опросе
        array('respondent/api/viewQuiz', 'pattern'=>'api/v1/quiz/<id:\d+>', 'verb'=>'GET'),

      # Получение комментариев к опросу
        array('respondent/api/listCommentsQuiz', 'pattern'=>'api/v1/quiz/<id:\d+>/<action:(comments)>', 'verb'=>'GET'),

      # Отправка комментариев к опросу
        array('respondent/api/createCommnetQuiz', 'pattern'=>'api/v1/quiz/<id:\d+>/<action:(comments)>', 'verb'=>'POST'),

      # Получение содержания опроса
        array('respondent/api/viewStructureQuiz', 'pattern'=>'api/v1/quiz/<id:\d+>/questions', 'verb'=>'GET'),

      # Получение статистики опроса
        array('respondent/api/viewStatisticsQuiz', 'pattern'=>'api/v1/quiz/<id:\d+>/stats', 'verb'=>'GET'),

      # Отправка фотографий анкеты
        array('respondent/api/createPhotoApplication', 'pattern'=>'api/v1/quiz/<id:\d+>/photos/<question>', 'verb'=>'POST'),

      # Отправка содержания анкеты
        array('respondent/api/updateApplication', 'pattern'=>'api/v1/quiz/<id:\d+>/<action:(application)>', 'verb'=>'POST'),

      # Получение истории замечаний по анкете
        array('respondent/api/listCommentsApplication', 'pattern'=>'api/v1/quiz/<id:\d+>/application/comments', 'verb'=>'GET'),

      # Отправка жалобы к анкете
        array('respondent/api/createAppealApplication', 'pattern'=>'api/v1/quiz/<id:\d+>/application/comments', 'verb'=>'POST'),

      # Отправка push notification
        array('respondent/api/sendPush', 'pattern'=>'api/v1/sendPush', 'verb'=>'GET'),

      );

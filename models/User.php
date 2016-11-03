<?php

namespace app\models;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\web\IdentityInterface;
use yii\Log;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property string $id
 * @property string $uname
 * @property string $upassw
 * @property string $grade
 * @property string $last_login
 * 
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
private $id;
private $username;
private $password;
private $authKey;
private $accessToken;



/**
 * @inheritdoc
 */
public static function tableName()
{
    return 'user';
}

/**
 * @inheritdoc
 */
public function rules()
{
    return [
        [['uname', 'upassw'], 'required'],
        [['uname'], 'string', 'max' => 25],
        [['upassw'], 'string', 'max' => 255]
    ];
}

/**
 * @inheritdoc
 */
public function attributeLabels()
{
    return [
        'id' => 'ID',
        'uname' => 'Uname',
        'upassw' => 'Upassw'
    ];
}

/**
 * @inheritdoc
 */
public static function findIdentity($id)
{
    return static::findOne($id);

}

/**
 * @inheritdoc
 */
public static function findIdentityByAccessToken($token, $type = null)
{
    return static::findOne(['access_token' => $token]);

}

/**
 * Finds user by username
 *
 * @param  string      $username
 * @return static|null
 */
public static function findByUsername($username)
{
      return static::findOne(['uname' => $username]);


}



/**
 * @inheritdoc
 */
public function getId()
{
    return $this->id;
}

/**
 * @inheritdoc
 */
public function getAuthKey()
{
    return $this->authKey;
}

/**
 * @inheritdoc
 */
public function validateAuthKey($authKey)
{
    return $this->authKey === $authKey;
}

/**
 * Validates password
 *
 * @param  string  $password password to validate
 * @return boolean if password provided is valid for current user
 */
public function validatePassword($password)
{
    Yii::trace("something went wrong".$this->password." and  ".$password);
    return $this->password == sha1($password);

}

/**

 * Generates password hash from password and sets it to the model

 *

 * @param string $password

 */

public function setPassword($password)

{

    $this->password_hash = Security::generatePasswordHash($password);

}



/**

 * Generates "remember me" authentication key

 */

public function generateAuthKey()

{

    $this->auth_key = Security::generateRandomKey();

}


}
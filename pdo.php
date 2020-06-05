<?php
class DB
{
	public static $dsn = 'mysql:dbname=u0483195_test;host=localhost';
	public static $user = 'u0483195_default';
	public static $pass = 'cZ4Exh8_';

	/**
	 * Объект PDO.
	 */
	public static $dbh = null;

	/**
	 * Statement Handle.
	 */
	public static $sth = null;

	/**
	 * Выполняемый SQL запрос.
	 */
	public static $query = '';

	/**
	 * Подключение к БД
	 */
	public static function getDbh()
	{
		if (!self::$dbh) {
			try {
				self::$dbh = new PDO(
					self::$dsn,
					self::$user,
					self::$pass,
					array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
				);
				self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			} catch (PDOException $e) {
				exit('Error connecting to database: ' . $e->getMessage());
			}
		}

		return self::$dbh;
	}

	/**
	 * Добавление в таблицу, в случаи успеха вернет вставленный ID, иначе 0.
	 */
	public static function add($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		return (self::$sth->execute((array) $param)) ? self::getDbh()->lastInsertId() : 0;
	}

	/**
	 * Выполнение запроса.
	 */
	public static function set($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		return self::$sth->execute((array) $param);
	}

	/**
	 * Получение строки из таблицы.
	 */
	public static function getRow($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetch(PDO::FETCH_ASSOC);
	}

	/**
	 * Получение всех строк из таблицы.
	 */
	public static function getAll($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetchAll(PDO::FETCH_ASSOC);
	}

	/**
	 * Получение значения.
	 */
	public static function getValue($query, $param = array(), $default = null)
	{
		$result = self::getRow($query, $param);
		if (!empty($result)) {
			$result = array_shift($result);
		}

		return (empty($result)) ? $default : $result;
	}

	/**
	 * Получение столбца таблицы.
	 */
	public static function getColumn($query, $param = array())
	{
		self::$sth = self::getDbh()->prepare($query);
		self::$sth->execute((array) $param);
		return self::$sth->fetchAll(PDO::FETCH_COLUMN);
	}
}

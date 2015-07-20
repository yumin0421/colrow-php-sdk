<?php

namespace Colrow;

class ColrowQuery
{
  private $where = [];
  private $orderby;
  private $reverse;
  private $offset;
  private $limit;

  public function equalTo($key, $value)
  {
    $this->where[$key] = $value;
    return $this;
  }

  private function addCondition($key, $condition, $value)
  {
    if (!isset($this->where[$key])) {
      $this->where[$key] = [];
    } else if (!is_array($this->where[$key])) {
      $temp = $this->where[$key];
      $this->where[$key] = ['=' => $temp];
    }
    $this->where[$key][$condition] = $value;
  }

  public function notEqualTo($key, $value)
  {
    $this->addCondition($key, '<>', $value);
    return $this;
  }

  public function lessThan($key, $value)
  {
    $this->addCondition($key, '<', $value);
    return $this;
  }

  public function greaterThan($key, $value)
  {
    $this->addCondition($key, '>', $value);
    return $this;
  }

  public function lessThanOrEqualTo($key, $value)
  {
    $this->addCondition($key, '<=', $value);
    return $this;
  }

  public function greaterThanOrEqualTo($key, $value)
  {
    $this->addCondition($key, '>=', $value);
    return $this;
  }

  public function orQuery()
  {
    $array = [];
    foreach (func_get_args() as $arg) {
      $array[] = $arg->where;
    }
    $this->where['_or'] = $array;
    return $this;
  }

  public function orderBy($value)
  {
    $this->orderby = $value;
    return $this;
  }

  public function reverse($flag)
  {
    $this->reverse = $flag ? 'true' : 'false';
    return $this;
  }

  public function offset($number)
  {
    $this->offset = $number;
    return $this;
  }

  public function limit($number)
  {
    $this->limit = $number;
    return $this;
  }

  public function find()
  {
    $options = $this->_getOptions();
    if (isset($options['where'])) {
      $options['where'] = json_encode($options['where']);
    }
    list($status_code, $response) = ColrowClient::_request('GET', $options);
    if ($status_code === 200) {
      return ColrowObject::_createObjectsFromFeeds($response['result']['feeds']);
    }
    return [];
  }

  public function _getOptions()
  {
    $options = [];
    if (!empty($this->where)) {
      $options['where'] = $this->where;
    }
    if ($this->orderby) {
      $options['orderby'] = $this->orderby;
    }
    if ($this->reverse) {
      $options['reverse'] = $this->reverse;
    }
    if ($this->offset) {
      $options['offset'] = $this->offset;
    }
    if ($this->limit) {
      $options['limit'] = $this->limit;
    }
    return $options;
  }
}
<?php

namespace Monaka;

class Form {

  public function create($dir = 'Monaka') {
    echo "<form action=\"./{$dir}/confirmation.php\" method=\"post\" enctype=\"multipart/form-data\">\n";
  }

  public function end($value = '確認') {
    echo "<div class=\"submit_area\">\n";
    echo "<input type=\"submit\" value=\"{$value}\" class=\"confirmation_btn\">\n";
    echo "</div>\n";
    echo "</form>\n";
  }

  public function inputName($name) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"名前\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function inputMail($name) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"メール\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function inputMailCheck($name) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"再入力\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function inputTel($name, $params = null) {
    $params = $params !== null ? ",".$params : $params;
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"電話番号{$params}\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function inputZip($name, $params = null) {
    $params = $params !== null ? ",".$params : $params;
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"郵便番号{$params}\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function inputText($name, $params = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params}\">\n";
    echo "<input type=\"text\" name=\"{$name}[value]\">\n";
  }

  public function select($name, $params1 = array(), $params2 = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    echo "<select name=\"{$name}[value]\">";
    foreach ($params1 as $key => $value) {
      if ($value !== "noValue") {
        echo "<option value=\"{$value}\">{$value}</option>";
      } else {
        echo "<option value=\"\">{$key}</option>";
      }
    }
    echo "</select>";
  }

  public function inputRadio($name, $params1 = array(), $params2 = null) {
    $checked = "checked";
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    foreach ($params1 as $key => $value) {
      if ($value === "text") {
        echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$key}\" {$checked}> {$key}</label>　\n";
        echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">　\n";
        echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
      } else {
        echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$value}\" {$checked}> {$value}</label>　\n";
      }
      $checked = "";
    }
  }

  public function inputRadioBR($name, $params1 = array(), $params2 = null) {
    $checked = "checked";
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    foreach ($params1 as $key => $value) {
      if ($value === "text") {
        echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$key}\" {$checked}> {$key}</label>　\n";
        echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">　\n";
        echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
      } else {
        echo "<label><input type=\"radio\" name=\"{$name}[value]\" value=\"{$value}\" {$checked}> {$value}</label>　\n";
      }
      echo "<br>\n";
      $checked = "";
    }
  }

  public function inputCheckbox($name, $params1 = array(), $params2 = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    foreach ($params1 as $key => $value) {
      if ($value === "text") {
        echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$key}\"> {$key}</label>　\n";
        echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">\n";
        echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
      } else {
        echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$value}\"> {$value}</label>　\n";
      }
    }
  }

  public function inputCheckboxBR($name, $params1 = array(), $params2 = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params2}\">\n";
    foreach ($params1 as $key => $value) {
      if ($value === "text") {
        echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$key}\"> {$key}</label>　\n";
        echo "<input type=\"text\" name=\"{$name}-{$key}[value]\">\n";
        echo "<input type=\"hidden\" name=\"{$name}-{$key}[params]\">\n";
      } else {
        echo "<label><input type=\"checkbox\" name=\"{$name}[value][]\" value=\"{$value}\"> {$value}</label>　\n";
      }
      echo "<br>\n";
    }
  }

  public function inputFile($name, $params = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params}\">\n";
    echo "<input type=\"file\" name=\"{$name}\"><br>";
  }

  public function textarea($name, $params = null) {
    echo "<input type=\"hidden\" name=\"{$name}[params]\" value=\"{$params}\">\n";
    echo "<textarea name=\"{$name}[value]\"></textarea>\n";
  }

  /**
   * JSON設定ファイル(例: Monaka/config/fields-sample.json)からフォーム項目を自動描画する。
   * PHPコードを触らずに項目の追加・削除・並び替えができるようにするための入口。
   */
  public function renderFromConfig($jsonPath) {
    $json = file_get_contents($jsonPath);
    if ($json === false) {
      throw new \RuntimeException("フォーム定義ファイルを読み込めません: {$jsonPath}");
    }
    $fields = json_decode($json, true);
    if (!is_array($fields)) {
      throw new \RuntimeException("フォーム定義ファイルの形式が正しくありません: {$jsonPath}");
    }
    foreach ($fields as $field) {
      $this->_renderField($field);
    }
  }

  private function _renderField($field) {
    $type = $field['type'] ?? 'text';
    $label = $field['label'] ?? '';
    $required = !empty($field['required']);
    $name = $field['name'] ?? $label;
    $requiredParam = $required ? '必須' : null;
    $labelText = $label . ($required ? '(必須)' : '');

    echo "<dl>\n";
    echo "<dt>" . h($labelText) . "</dt>\n";
    echo "<dd>\n";

    switch ($type) {
      case 'name':
        $this->inputName($name);
        break;
      case 'mail':
        $this->inputMail($name);
        break;
      case 'mailCheck':
        $this->inputMailCheck($name);
        break;
      case 'tel':
        $this->inputTel($name, $requiredParam);
        break;
      case 'zip':
        $this->inputZip($name, $requiredParam);
        break;
      case 'text':
        $this->inputText($name, $requiredParam);
        break;
      case 'textarea':
        $this->textarea($name, $requiredParam);
        break;
      case 'file':
        $this->inputFile($name, $requiredParam);
        break;
      case 'select':
        $this->select($name, $this->_optionsToSelectParams($field['options'] ?? array()), $requiredParam);
        break;
      case 'radio':
        $this->inputRadio($name, $this->_optionsToChoiceParams($field['options'] ?? array()), $requiredParam);
        break;
      case 'radioBR':
        $this->inputRadioBR($name, $this->_optionsToChoiceParams($field['options'] ?? array()), $requiredParam);
        break;
      case 'checkbox':
        $this->inputCheckbox($name, $this->_optionsToChoiceParams($field['options'] ?? array()), $requiredParam);
        break;
      case 'checkboxBR':
        $this->inputCheckboxBR($name, $this->_optionsToChoiceParams($field['options'] ?? array()), $requiredParam);
        break;
      default:
        throw new \RuntimeException("未対応のフォーム項目タイプです: {$type}");
    }

    echo "</dd>\n";
    echo "</dl>\n";
  }

  private function _optionsToSelectParams($options) {
    $params = array();
    foreach ($options as $option) {
      $label = $option['label'] ?? '';
      if (!empty($option['placeholder'])) {
        $params[$label] = 'noValue';
      } else {
        $params[] = $label;
      }
    }
    return $params;
  }

  private function _optionsToChoiceParams($options) {
    $params = array();
    foreach ($options as $option) {
      $label = $option['label'] ?? '';
      if (!empty($option['freeText'])) {
        $params[$label] = 'text';
      } else {
        $params[] = $label;
      }
    }
    return $params;
  }

}

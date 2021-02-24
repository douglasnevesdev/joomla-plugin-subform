<?php

use Joomla\CMS\MVC\Model\BaseDatabaseModel;


defined('_JEXEC') or die;

JLoader::import('components.com_fields.libraries.fieldsplugin', JPATH_ADMINISTRATOR);

class PlgFieldsSubforms extends FieldsPlugin
{

  
  /**
	 * Transforma o campo em um elemento XML DOM e o anexa como filho no pai fornecido..
	 *
	 * @param   stdClass    $field   O campo.
	 * @param   DOMElement  $parent  O pai do nó do campo.
	 * @param   JForm       $form    O formulario
	 *
	 * @return  DOMElement
	 *
	 * @since   3.9.0
	 */
  public function onCustomFieldsPrepareDom($field, DOMElement $parent, JForm $form)
  {
    $fieldNode = parent::onCustomFieldsPrepareDom($field, $parent, $form);

    if (!$fieldNode)
    {
      return $fieldNode;
    }

    $readonly = false;

    if (!FieldsHelper::canEditFieldValue($field))
    {
      $readonly = true;
    }

    //Cria xml do subform
    $fieldNode->setAttribute('type', 'subform');
    $fieldNode->setAttribute('multiple', $field->fieldparams->get('fieldmultiple'));
    $fieldNode->setAttribute('layout', $field->fieldparams->get('fieldlayout'));
    $fieldNode->setAttribute('formsource', $field->fieldparams->get('fieldsubform'));
    $fieldNode->setAttribute('min', $field->fieldparams->get('fieldmin'));
    $fieldNode->setAttribute('max', $field->fieldparams->get('fieldmax'));

    // Return the node
    return $fieldNode;
  }



/**
 * O evento de salvamento.
 *
 * @param   string   $context  O contexto
 * @param   JTable   $item     Os dados do artigo
 * @param   boolean  $isNew    É novo item
 * @param   array    $data     Valida os dados
 *
 * @return  boolean
 *
 * @since   3.9.0
 */
public function onContentAfterSave($context, $item, $isNew, $data = array())
{
  // Crie o contexto correto para a categoria
  if ($context == 'com_categories.category')
  {
    $context = $item->get('extension') . '.categories';

    // Defina o catid na categoria para obter apenas os campos que pertencem a esta categoria
    $item->set('catid', $item->get('id'));
  }

  // Verifique o contexto
  $parts = FieldsHelper::extract($context, $item);

  if (!$parts)
  {
    return true;
  }

  // Compile o contexto certo para os campos
  $context = $parts[0] . '.' . $parts[1];

  // Carregando os campos
  $fields = FieldsHelper::getFields($context, $item);

  if (!$fields)
  {
    return true;
  }

  // Obtenha os dados dos campos
  $fieldsData = !empty($data['com_fields']) ? $data['com_fields'] : array();

  // Carregando o modelo
  /** @var FieldsModelField $model */
  $model = BaseDatabaseModel::getInstance('Field', 'FieldsModel', array('ignore_request' => true));

  // Percorrer os campos
  foreach ($fields as $field)
  {
    // Encontre o campo deste tipo repeatable
    if ($field->type !== $this->_name)
    {
      continue;
    }

    // Determine o valor se ele estiver disponível nos dados
    $value = key_exists($field->name, $fieldsData) ? $fieldsData[$field->name] : null;

    // Lidar com valores codificados em json
    if (!is_array($value))
    {
      $value = json_decode($value, true);
    }

    // Definir o valor do campo e do item
    $model->setFieldValue($field->id, $item->get('id'), json_encode($value));
  }

  return true;
}


}

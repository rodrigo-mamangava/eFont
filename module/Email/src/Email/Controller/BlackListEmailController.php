<?php

namespace Email\Controller;

/**
 * Controle da Backlist Email
 * @author Claudio
 */
class BlackListEmailController extends \Useful\Controller\ControlController
{
	/**
	 * Retorna todos os item, de acordo com a consulta
	 * @param unknown $item
	 * @param number $count
	 * @param number $offset
	 */
	public function fetchAll($item, $count = 1, $offset = 0){
		return $this->getDbTable ('Email\Model\BlackListEmailTable')->fetchAll($item, $count, $offset);
	}
	/**
	 * Retorna um item de acordo com a consulta
	 * @param unknown $item
	 */
	public function fetch($item){
		return $this->getDbTable ('Email\Model\BlackListEmailTable')->fetch($item);
	}
	/**
	 * Adiciona um item
	 * @param unknown $item
	 */
	public function create($item){
		return $this->getDbTable ('Email\Model\BlackListEmailTable')->create($item);
	}
	/**
	 * Desabilita um item
	 * @param unknown $item
	 */
	public function removed($item){
		return $this->getDbTable ('Email\Model\BlackListEmailTable')->removed($item);
	}
}
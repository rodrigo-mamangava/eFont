<?php

namespace Application\Controller;

use \Validador\Controller\ValidadorController;
use \Useful\Controller\UsefulController;

/**
 * Detalhes do produto
 *
 * @author Claudio
 *        
 */
class ShopProductDetailsController extends ApplicationController {

    /**
     *
     * {@inheritDoc}
     *
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction() {
        return $this->viewModel->setTerminal(true);
    }

    public function detalhesAction() {
        $Params = $this->params();
        $id = $Params->fromQuery('id', null);

        $Projects = new \Shop\Controller\ProjectsController($this->getServiceLocator());

        //copiando a font de /temp para /public/font-test        
        $objFont = $Projects->find($id, null);
        $fontTemp = $objFont['ddig'];      
        $newfile = "public/font-test/font-test.tiff";        
        copy($fontTemp, $newfile);
    
        $this->viewModel->setVariable('objFont', $Projects->find($id, null));

        return $this->viewModel->setTerminal(true);
    }

    /**
     * Obtem dados de um item
     */
    public function editAction() {
        // Default
        $data = $this->translate("Unknown Error, try again, please.");
        $outcome = $status = false;
        // GET
        $Params = $this->params();
        $id = $Params->fromQuery('id', null);
        if (!ValidadorController::isValidDigits($id)) {
            $data = $this->translate('Invalid Id');
        } else {
            // Default
            $project = null;
            $base_fonts = $base_styles = $base_formats = $collections = $styles = $fonts = $families = $formats = $licenses = array();
            // Controllers
            $Projects = new \Shop\Controller\ProjectsController($this->getServiceLocator());
            $ProjectHasLicense = new \Shop\Controller\ProjectHasLicenseController($this->getServiceLocator());
            $LicenseHasFormats = new \Shop\Controller\LicenseHasFormatsController($this->getServiceLocator());
            $ProjectHasFamiliy = new \Shop\Controller\ProjectHasFamilyController($this->getServiceLocator());
            $FamilyHasFormats = new \Shop\Controller\FamilyHasFormatsController($this->getServiceLocator());
            $FontStyles = new \Shop\Controller\FontStylesController($this->getServiceLocator());
            // BEGIN PROJETO
            $rs = $Projects->find($id, null);

            if ($rs) {
                // PROJETO
                $project = UsefulController::getStripslashes($rs);

                $fontSrc = \Cryptography\Controller\CryptController::decrypt($project->ddig, true);
                $project_id = $project ['id'];
                $project ['collection'] = 0;

                // BEGIN LICENCAS
                $rs_licenses = $ProjectHasLicense->fetchAllShop($project_id);
                if ($rs_licenses->count() > 0) {
                    $rs_licenses = iterator_to_array($rs_licenses->getCurrentItems());
                    foreach ($rs_licenses as $lc_item) {
                        $licenses [$lc_item->license_id] = UsefulController::getStripslashes($lc_item);

                        if (!isset($project ['license'])) { // Default
                            $project ['license'] = $lc_item->license_id;
                        }

                        // BEGIN FORMATS
                        $rs_formats = $LicenseHasFormats->fetchAllShop($lc_item->license_id);
                        if ($rs_formats->count() > 0) {
                            $rs_formats = iterator_to_array($rs_formats->getCurrentItems());
                            foreach ($rs_formats as $ft_item) {
                                $formats [$lc_item->license_id] [$ft_item->license_formats_id] [$ft_item->id] = UsefulController::getStripslashes($ft_item);

                                if (!isset($project ['format'] [$lc_item->license_id] [$ft_item->license_formats_id])) {
                                    $project ['format'] [$lc_item->license_id] [$ft_item->license_formats_id] = $ft_item->id;
                                }
                            }
                        }
                        // END FORMATS
                    }
                }
                // END LICENCAS
                // BEGIN FAMILIA
                $rs_families = $ProjectHasFamiliy->fetchAllShop($project_id);
                if ($rs_families->count() > 0) {
                    $rs_families = iterator_to_array($rs_families->getCurrentItems());
                    foreach ($rs_families as $f_item) {

                        $f_item ['collection'] = 0;
                        $f_item ['subprice'] = 0;
                        $f_item ['check_collection'] = false;
                        $f_item ['collapsed'] = true;

                        $families [$f_item->id] = UsefulController::getStripslashes($f_item);
                        // BEGIN BASE FORMATS
                        $rs_base_formats = $FamilyHasFormats->fetchAll($project->company_id, $f_item->id, $project_id);
                        if ($rs_base_formats->count() > 0) {
                            $rs_base_formats = iterator_to_array($rs_base_formats->getCurrentItems());
                            foreach ($rs_base_formats as $bf_item) {
                                $base_formats [$f_item->id] [$bf_item->license_formats_id] = $bf_item->number_files;
                            }
                        }
                        // END BASE FORMATS
                        $project ['families'] ++;
                    }
                }
                // END FAMILIA
                // BEGIN STYLES
                $rs_styles = $FontStyles->fetchAllShop($project->company_id, $project_id);
                if ($rs_styles->count() > 0) {
                    $rs_styles = iterator_to_array($rs_styles->getCurrentItems());
                    foreach ($rs_styles as $s_item) {
                        $base_styles [$s_item->family_id] [$s_item->formats_id] [$s_item->id] = $s_item->font_price;

                        $font_subfamily = strtolower(ValidadorController::removeBlank($s_item->font_subfamily));

                        $base_fonts [$s_item->family_id] [$font_subfamily] ['pricing'] [$s_item->formats_id] [$s_item->id] = $s_item->font_price;
                        $base_fonts [$s_item->family_id] [$font_subfamily] ['font_subfamily'] = $s_item->font_subfamily;
                        $base_fonts [$s_item->family_id] [$font_subfamily] ['disabled'] = false;
                        $base_fonts [$s_item->family_id] [$font_subfamily] ['selected'] = false;
                    }
                }
                // END STYLES
                // BEGIN COLLECTIONS
                foreach ($formats as $ft_lc_key => $ft_lc_item) { // Formato x Licenca
                    foreach ($ft_lc_item as $ft_fi_key => $ft_fi_item) { // Formato x Tipo
                        foreach ($ft_fi_item as $ft_pr_key => $ft_pr_item) { // Formato x Multiplicador
                            $m = $ft_pr_item->multiplier;

                            foreach ($families as $f_key => $f_item) { // Licenca x Formato
                                $p = $f_item->money_family;

                                if (isset($base_formats [$f_key] [$ft_fi_key])) { // Existe o formato carregado?
                                    $collections [$ft_lc_key] [$ft_fi_key] [$ft_pr_key] [$f_key] = sprintf("%.2f", floatval($m) * floatval($p));

                                    if (isset($base_styles [$f_key] [$ft_fi_key])) { // Existe o estilo
                                        $style = $base_styles [$f_key] [$ft_fi_key];
                                        foreach ($style as $s_key => $s_item) {
                                            $styles [$ft_lc_key] [$ft_fi_key] [$ft_pr_key] [$f_key] [$s_key] = sprintf("%.2f", floatval($m) * floatval($s_item));
                                        }
                                    }

                                    if (isset($base_fonts [$f_key])) { // Existe a fonte?
                                        $font = $base_fonts [$f_key];
                                        foreach ($font as $fn_key => $fn_item) {
                                            if (isset($fn_item ['pricing'] [$ft_fi_key])) {
                                                foreach ($fn_item ['pricing'] [$ft_fi_key] as $fp_key => $fp_item) {
                                                    $fonts [$ft_lc_key] [$ft_fi_key] [$ft_pr_key] [$f_key] [$fn_key] [$fp_key] = sprintf("%.2f", floatval($m) * floatval($fp_item));
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                // END COLLECTIONS
                // Retorno
                $outcome = $status = true;
                $data = array(
                    'project' => $project,
                    'licenses' => $licenses,
                    'formats' => $formats,
                    'families' => $families,
                    'collections' => $collections,
                    'pricing' => $styles,
                    'styles' => $base_fonts,
                    'fonts' => $fonts,
                    'font_src' => $fontSrc
                );
                // END PROJETO
            } else {
                $data = $this->translate('Invalid Id');
            }
        }
        // Response
        self::showResponse($status, $data, $outcome, true);
        die();
    }

}

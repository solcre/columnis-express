<?php

namespace Columnis\Model;

class PageLegacyData {

    /**
     *
     * @var array 
     */
    protected $data;

    public function setData($data) {
        $this->data = $data;
    }

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Get legacy data
     * 
     * @return array
     */
    public function getData() {
        $legacyData = [
            "sitio" => [
                "traducciones" => [],
                "traducciones_json" => [],
                "menus" => [],
                "datos_fecha" => [],
                "images_path" => '',
                "idiomas" => [],
                "sitelink" => '',
                "head" => [],
            ],
            "modulos" => [],
            "pagina" => [],
        ];

        if(is_array($this->data)) {
            foreach($this->data as $key => $value) {

                switch($key) {
                    case 'columnis.rest.pages':
                        $this->processPageData(array_values($value)[0], $legacyData['pagina']);
                        break;
                    case 'columnis.rest.menu_items':
                        $this->processMenuData(array_values($value)[0], $legacyData['sitio']['menus'][0]);
                        break;
                    case 'columnis.rest.configuration':
                        $this->processConfigurationData(array_values($value)[0], $legacyData);
                        break;
                    case 'columnis.rest.translations':
                        $this->processTranslationsData(array_values($value)[0], $legacyData);
                        break;
                    case 'columnis.rest.sections':
                        $this->processSectionsData($value, $legacyData['modulos']);
                        break;
                    case 'columnis.rest.banners':
                        $this->processBannersData($value, $legacyData['modulos']);
                        break;
                }
            }
        }

        return $legacyData;
    }

    protected function processPageData($value, &$pageData) {

        if(!empty($value)) {
            $pageData['idPagina'] = $value['id'];
            $pageData['template'] = $value['template'];
            $pageData['tituloPagina'] = $value['text'];
            $pageData['description'] = $value['description'];
            $pageData['keywords'] = $value['keywords'];
        }
    }

    protected function processMenuData($values, &$menusData) {
        $menusData = [
            'vinculos' => []
        ];

        if(is_array($values["_embedded"]["menu_items"])) {
            foreach($values["_embedded"]["menu_items"] as $menu) {
                $menusData['vinculos'][] = [
                    "id" => $menu['id'],
                    "texto" => $menu['text'],
                    "target" => $menu['target'],
                    "anclaje" => $menu['anchorage'],
                ];
            }
        }
    }

    protected function processConfigurationData($values, &$data) {

        $data["sitio"]["images_path"] = $values["imagesDomain"];

        $data["sitio"]["head"] = [
            "fixed" => [
                "title" => $values["title"],
                "keywords" => $values["keywords"],
                "description" => $values["description"]
            ],
            "analytics" => $values["analyticsAccountIds"]
        ];

        $data["sitio"]["sitelink"] = $_SERVER['SERVER_NAME'];
    }

    protected function processTranslationsData($values, &$data) {
        $translations = [];

        if(is_array($values["_embedded"]["translations"])) {
            foreach($values["_embedded"]["translations"] as $translation) {

                if($translation["languageId"] === "es") {
                    $translations = $translation["translations"];
                    break;
                }
            }
        }

        $data["sitio"]["traducciones"] = $translations;
        $data["sitio"]["traducciones_json"] = json_encode($translations);
    }

    protected function processSectionsData($values, &$modulesData) {
        if(!array_key_exists("secciones", $modulesData)) {
            $modulesData["secciones"] = [];
        };

        foreach($values as $route => $data) {
            $modulesData["secciones"][] = [
                "id" => $data["id"],
                "titulo" => $data["title"],
                "keywords" => $data["keywords"],
                "descripcion" => $data["description"],
                "foto" => $this->processPictureData($data["_embedded"]['picture']),
                "subSecciones" => $data["subSections"],
            ];
        }
    }

    protected function processBannersData($values, &$modulesData) {
        if(!array_key_exists("banners", $modulesData)) {
            $modulesData["banners"] = [
                "grupos_banners" => []
            ];
        };


        foreach($values as $bannerGroup) {
            $modulesData["banners"]["grupos_banners"][] = [
                "nombre" => "",
                "banners" => []
            ];

            $bannerIndex = count($modulesData["banners"]["grupos_banners"]) - 1;

            if(is_array($bannerGroup["_embedded"]["banners"])) {
                foreach($bannerGroup["_embedded"]["banners"] as $route => $data) {
                    
                    $picture = $this->processPictureData($data["_embedded"]['picture']);
                    
                    $modulesData["banners"]["grupos_banners"][$bannerIndex]['banners'][] = [
                        "id" => $data["id"],
                        "titulo" => $data["title"],
                        "texto" => $data["text"],
                        "vinculo" => $data["link"],
                        "descripcion" => $data["description"],
                        "enImagen" => $data["inImagen"],
                        "archivo" => is_array($picture) ? $picture["src"] : "",
                        "foto" => $picture,
                    ];
                }
            }
        }
        
    }

    protected function processPictureData($picture) {
        if(!is_array($picture)) {
            return;
        }
        
        return [
            "id" => $picture["id"],
            "idOriginal" => $picture["id"],
            "titulo" => $picture["title"],
            "src" => $picture["filename"],
            "src_alternativa" => $picture["filename"],
            "comentarios" => $picture["description"],
            "orden" => $picture["order"],
        ];
    }
}

?>
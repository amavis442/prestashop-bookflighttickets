<?php
class RouteHelper
{
    private $origin;
    private $destination;
    private $location;
    private $route;
    private $_stack = array();
    private $id_routes = array();
    private $table_route;

    public function __construct()
    {
        $this->table_route = _DB_PREFIX_.'booking_route';
    }
    
    public function setOrigin($id)
    {
        if (!is_integer($id)) {
            throw new Exception('Start moet een integer zijn Opgegeven: '.$id);
        }
        $this->origin = $id;
    }
    
    public function setDestination($id)
    {
        if (!is_integer($id)) {
            throw new Exception('Einbestemming moet een integer zijn. Opgegeven: '.$id);
        }
    
        $this->destination = $id;
    }
    
    public function setOriginDestination($origin=0, $destination=0)
    {
        try {
            $this->setOrigin($origin);
            $this->setDestination($destination);
        } catch (PrestaShopModuleException $e) {
            throw $e;
        }
    }
    
    public function findAllRoute($where)
    {
        $sql = 'select * from '.$this->table_route.' WHERE '.$where;
        $records = Db::getInstance()->executeS($sql);
        return $records;
    }
    
    
    
    /**
     *
     * @param number $iteraties
     * @param number $index
     * @return multitype:
     */
    public function getRoutes($iteraties = 0,$index = 0) {
        $this->_stack = array();
        $ret = array();
    
        $child_nodes = $this->findAllRoute(sprintf('id_location_1=%d',$this->origin));
        //var_dump($child_nodes);
        foreach ($child_nodes as $k=>$parent_node) {
            try {
                $this->_getRoutes($parent_node,0);
            } catch (PrestaShopDatabaseException $e) {
                throw $e;
            }
        }
        //var_dump($this->_stack);
        if ($this->_stack && count($this->_stack) > 0) {
            return $this->_stack;
        } else {
            /* No route to target */
            return false;
        }
    }
    
    /**
     *
     * @param CActiveRecord $parent_node
     * @param number $destination
     * @param number $it
     * @param array $tmp
     * @throws Exception
     * @return boolean
     */
    private function _getRoutes(Array $parent_node, $it = 0, Array $tmp = null) {
        /* echo '<p>Iteratie :'.$it.'</p>';
        var_dump($parent_node);
        var_dump($this->_stack);
        echo '<hr>';
        */
        
        if ($it === 0) {
            $this->id_routes = array();
            $tmp = array();
        }
        
        if (in_array($parent_node['id_route'],$this->id_routes)) {
            $tmp = array();
            return false;
        }
        $this->id_routes[] = $parent_node['id_route'];
        
        if ($it === 0 && (int)$parent_node['id_location_2'] == $this->destination) {
            /* Rechtstreekse verbinding gevonden */
            $this->_stack[]= array($parent_node['id_route']);
            $tmp = array();
        } else {
            $child_nodes = $this->findAllRoute(sprintf('id_location_1=%d',$parent_node['id_location_2']));
            if ($child_nodes) {
                $num_nodes = count($child_nodes);
    
                foreach ($child_nodes as $k=>$child_node) {
                    if ($child_node['id_location_2'] == $this->origin) {
                        $tmp = array();
                        --$it;
                        continue;
                    }
                    
                    
                    /* Checken of we de eindbestemming hebben bereikt */
                    if ((int)$child_node['id_location_2'] == $this->destination) {
                        $tmp[] = $child_node['id_route'];
                        $this->_stack[] = $tmp;
                        $tmp = array();
                        --$it;
                        continue;
                    } else {
                        if ($it < 100) {
                            if ($num_nodes == 1) {
                                if ($this->checkNode($parent_node, $child_node)) {
                                    $tmp[] = (int)$child_node['id_route'];
                                    $this->_stack[] = $tmp;
                                    unset($tmp);
                                }
                                return false;
                            } else {
                                $tmp[] = (int)$parent_node['id_route'];
                                $res = $this->_getRoutes($child_node,++$it,$tmp);
    
                                if (!$res && $num_nodes == 1) {
                                    $it = 0;
                                    return false;
                                } else {
                                    --$it;
                                    continue;
                                }
                            }
                        } else {
                            throw new PrestaShopModuleException('Aantal iteraties overschrijdt maximum van 100');
                            return false;
                        }
                    }
                }
            } else {
                /* Geen andere knooppunten meer. */
                return false;
            }
        }
        return false;
    }
    
    
    /**
     * Check of een parent en child naar elkaar verwijzen of dat we het einde hebben bereikt maar nog
     * niet het doel.
     *
     * @param CActiveRecord $parent_node
     * @param CActiveRecord $child_node
     * @return boolean
     */
    protected function checkNode(Array $parent_node,Array $child_node)
    {
        /* Hier gaat ie rondzingen */
        if ($parent_node['id_location_2'] == $this->destination){
            return true;
        } else if ((int)$child_node['id_location_2'] === $this->destination) {
            return true;
        }
        
        return false;
        
    }
    
    
}
<?php
/**
 * A Forest
 * 
 * @author Nicola Da Rold
 */
class Forest {
    const MIN_X = 40;
    const MIN_Y = 40;
    const MAX_X = 50;
    const MAX_Y = 50;
    const TREE_PROBABILITY = 90;    // tree probability for a row
    
    
    
    /**
     * @var $x integer
     */
    protected $x;
    /**
     * @var $y integer
     */
    protected $y;
    /**
     * @var $trees array
     */
    protected $trees = array();
    
    
    
    /**
     * Create a random sized forest and populate it
     */
    function __construct() {
        $this->x = rand(self::MIN_X, self::MAX_X-1);
        $this->y = rand(self::MIN_Y, self::MAX_Y-1);
        $this->growRandomTrees();
    }
    
    
    
    function getX() {
        return $this->x;
    }
    function getY() {
        return $this->y;
    }
    function getTrees() {
        return $this->trees;
    }
    function setX($x) {
        $this->x = $x;
        return $this;
    }
    function setY($y) {
        $this->y = $y;
        return $this;
    }


    
    
    
    /**
     * Populate the forest with a random # of trees
     */
    protected function growRandomTrees()
    {
        $availableY = array();
        // build available Y positions array
        for ($i=0; $i<$this->y; $i++) {
            $availableY[] = $i;
        }
        // for each row
        for ($i=0; $i<$this->x; $i++) {
            // a row can have no tree
            if ((count($availableY) > 0) && 
                (rand(0, 99) < self::TREE_PROBABILITY)) {
                // put a tree in a random Y position never used before
                $randomY = rand(0, count($availableY)-1);
                $this->trees[$i] = $availableY[$randomY];
                // remove position so it's not reused
                unset($availableY[$randomY]);
                $availableY = array_values($availableY);    // rebase array
            } else {
                $this->trees[$i] = null;
            }
        }
    }
    
    
    
    
    /**
     * Count the trees cuttable within a rectangular area
     * 
     * @return integer Cur trees count
     */
    public function countCutTrees($x1,$y1, $x2,$y2)
    {
        $cutTrees = 0;
        // swap coordinates if in wrong order
        if ($x1 > $x2) {
            list($x1, $x2) = array($x2, $x1);
        }
        if ($y1 > $y2) {
            list($y1, $y2) = array($y2, $y1);
        }
        // for each row
        for ($x=$x1; $x<=$x2; $x++) {
            // if there is a tree
            if (($this->trees[$x] !== null) && ($this->trees[$x] >= $y1) && ($this->trees[$x] <= $y2)) {
                $cutTrees++;    // count it
            }
        }
        
        return $cutTrees;
    }
    
    
    
    
    
    /**
     * Printout the forest map
     */
    public function output()
    {
        echo "(" . $this->x . "x" . $this->y . ")\n";
        for ($x=0; $x < $this->x; $x++) {
            if ($this->trees[$x] !== null) {
                // posizione albero
                //echo sprintf("%02d", $this->trees[$x]) . " - ";
                // punti prima dell'albero
                if ($this->trees[$x] > 0) {
                    echo str_repeat(".", $this->trees[$x]);
                }
                // albero
                echo "*";
                // punti dopo l'albero
                if ($this->trees[$x] < $this->y) {
                    echo str_repeat(".", $this->y - $this->trees[$x] -1);
                }
            } else {
                //echo "xx - ";
                echo str_repeat(".", $this->y);
            }
            echo "\n";
        }
    }
    
    
    
    
}




/**
 * Find all areas that can be cut to obtain K trees.
 * The 2 defining area vertexes ust contain a tree
 * 
 * @param Forest $f
 * @param integer $k Nr of trees to cut
 * @return array Areas to be cut
 * 
 * Note: result is in the form:
 * $areas = array(
 *      array(x1,y1, x2,y2),
 *      array(x1,y1, x2,y2),
 *      ...
 * )
 */
function findAreas(Forest $f, $k)
{
    $areas = array();
    $trees = $f->getTrees();
    // for each row
    for ($x=0; $x<$f->getX(); $x++) {
        // if there is a tree
        if ($trees[$x] !== null) {
            //echo "Try edge (" . $x . "," . $trees[$x] . ")\n";
            // for each remaining row
            for ($nextX=$x; $nextX<$f->getX(); $nextX++) {
                // if there is a tree
                if ($trees[$nextX] !== null) {
                    //echo "    (" . $nextX . "," . $trees[$nextX] . "): ";
                    // if y2 > y1
                    if ($trees[$nextX] > $trees[$x]) {
                        // cut tree area
                        $cutTrees = $f->countCutTrees($x, $trees[$x], $nextX, $trees[$nextX]);
                        // if cut trees # === $k
                        if ($cutTrees === $k) {
                            // add area to results
                            $areas[] = array($x, $trees[$x], $nextX, $trees[$nextX]);
                        }
                        //echo $cutTrees;
                    }
                    //echo "\n";
                }
            }
        }
    }
    
    return $areas;
}



// plant a forest 
$f = new Forest();
$f->output();

// dump areas with K trees to cut
var_dump(findAreas($f, 10));
?>
<?php

class Pager {

    private $totalpages;

    public function __construct($rowsnumber, $rowsperpage) {
        $this->totalpages = ceil($rowsnumber / $rowsperpage);
    }

    public function pageSelector() {
        ?>
        <div class="pager">
            <div class="pn" id="leftarrow">&Larr;</div> 
            <?php
            for ($i = 0; $i < $this->totalpages; $i++) {
                ?>
                <div class="pn ps" data-pagenumber="<?php echo $i; ?>"><?php echo $i + 1; ?></div>
                <?php
            }
            ?>
            <div class="pn" id="rightarrow">&Rarr;</div> 
        </div>
        <?php
    }

}

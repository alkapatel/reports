@if(isset($pageTitle) && isset($bred_crumb_array))
<div class="breadcrumbs">
    <div class="container">
        <h1 class="pull-left">{{ $pageTitle }}</h1>
        <ul class="pull-right breadcrumb">
            <?php 
                $last = count($bred_crumb_array);
                $count = 1;
                foreach ($bred_crumb_array as $key => $value) {
                    ?>
                        <li class="<?php echo $last == $count ? 'active':'';?>">                            
                            <?php if($value != ""):?>
                            <a href="<?php echo $value == "" ? '#':$value;?>"><?php echo $key;?></a>
                            <?php else:?>
                            <?php echo $key;?>
                            <?php endif;?>
                        </li>            
                    <?php
                    $count++;
                }
            ?>
            
        </ul>
    </div>
</div>
@endif

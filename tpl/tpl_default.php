<div class="c-nbu">
    <ul>
        <?php if($usd){ ?>
            <li>
                <span class="c-nbu__name">USD:</span> <span class="c-nbu__rate"><?php echo $data['usd']; ?></span>
            </li>
        <?php } ?>
        <?php if($eur){ ?>
            <li>
                <span class="c-nbu__name">EUR:</span> <span class="c-nbu__rate"><?php echo $data['eur']; ?></span>
            </li>
        <?php } ?>
        <?php if($pln){ ?>
            <li>
                <span class="c-nbu__name">PLN:</span> <span class="c-nbu__rate"><?php echo $data['pln']; ?></span>
            </li>
        <?php } ?>
    </ul>
    <?php if($showDate){ ?>
        <div class="c-nbu__date">
            <?php echo $data['date']; ?>
        </div>
    <?php } ?>  
</div>
<div>
    <h3><?= $user ?> : History</h3>
</div>
<div>
    <table style="margin-left:auto; margin-right:auto">
        <tr>
            <td style="text-align:center;padding-bottom:10px"><b>Symbol</b></td>
            <td style="text-align:center;padding-bottom:10px"><b>Name</b></td>
            <td style="text-align:center;padding-bottom:10px"><b>Shares</b></td>
            <td style="text-align:center;padding-bottom:10px"><b>Price</b></td>
            <td style="text-align:center;padding-bottom:10px"><b>Value</b></td>
            <td style="text-align:center;padding-bottom:10px"><b>Time</b></td>
        </tr>
        <?php foreach($properties as $property): /*padding: 0px 20px 20px 0px;*/?>   
            <tr>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><?= $property["symbol"] ?></td>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><a <?= 'href="quote.php?symbol='.$property['symbol'].'"' ?>><?= $property["name"] ?></a></td>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><?= $property["shares"] ?></td>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><?= money_format('%i',$property["price"]) ?></td>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><?= money_format('%i',$property["value"]) ?></td>
                <td style= "text-align:center;padding-right: 20px; padding-left:20px"><?= $property["time"] ?></td>
            </tr>
        <?php endforeach?>
    </table>
</div>
<br/>
<div>
    <p><a href="logout.php">Log Out</a>  |  <a href = "quote.php">Quote</a>  |  <a href = /index.php>Portfolio</a></p>
</div>

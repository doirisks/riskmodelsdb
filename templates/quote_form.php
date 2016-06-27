<div>
    <?= htmlspecialchars($_SESSION["stock"]["symbol"]) . "   " . htmlspecialchars($_SESSION["stock"]["name"]) . "   ". htmlspecialchars(money_format('%i',$_SESSION["stock"]["price"])) ?>
</div>
<div>
    <p>You have <?= htmlspecialchars($shares) ?> shares in <?= htmlspecialchars($_SESSION["stock"]["symbol"]) ?> </p>
</div>
<br/>
<form action="quote.php" method="post">
    <fieldset>
        <div class="form-group">
            <input autofocus class="form-control" name="number" placeholder="how many?" type="integer"/>
        </div>
        <div class="form-group">
            <select autofocus class="form-control" name="buy_sell">
                <option>Buy</option>
                <option>Sell</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default">Buy/Sell</button>
        </div>
    </fieldset>
</form>
<br/>
<div>
    <p><a href="logout.php">Log Out</a>  |  <a href = "quote.php">Quote</a>  |  <a href = /index.php>Portfolio</a></p>
</div>

<div>
    <h3><?= $user ?> : Change Password</h3>
</div>
<br/>
<form action="passchange.php" method="post">
    <fieldset>
        <div class="form-group">
            <input autofocus class="form-control" name="old_password" placeholder="Old Password" type="password"/>
        </div>
        <br/>
        <div class="form-group">
            <input class="form-control" name="password" placeholder="New Password" type="password"/>
        </div>
        <div class="form-group">
            <input class="form-control" name="confirmation" placeholder="Confirm New Password" type="password"/>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-default">Change Password</button>
        </div>
    </fieldset>
</form>
<br/>
<div>
    <p><a href="logout.php">Log Out</a>  |  <a href = "quote.php">Quote</a>  |  <a href = "/index.php">Portfolio</a>  |  <a href = "history.php">History</a></p>
</div>

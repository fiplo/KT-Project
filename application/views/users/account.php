<div class="container">
    <h2>Sveiki <?php echo $user['first_name']; ?>!</h2>
    <a href="<?php echo base_url('users/logout'); ?>" class="logout">Atsijungti</a>
    <div class="regisFrm">
        <p><b>Vardas: </b><?php echo $user['first_name'].' '.$user['last_name']; ?></p>
        <p><b>El. Pastas: </b><?php echo $user['email']; ?></p>
        <p><b>Tel. Numeris: </b><?php echo $user['phone']; ?></p>
        <p><b>Lytis: </b><?php echo $user['gender']; ?></p>
    </div>
</div> 

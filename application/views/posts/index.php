<div class="container">
    <h2>Skelbimu Sarasas</h2>
<div class="container">
    <table>
        <?php foreach($data as $entry) { ?>
            <td><?php echo $entry['postname'] ?></td> 
            <td><?php echo $entry['postdesc'] ?></td> 
            <td><?php echo $entry['modified'] ?></td> 
            <td><?php echo $entry['path'] ?></td> 
        <?php } ?>
    </table>
</div>

<p><a href="<?php echo base_url('users/login'); ?>">Prisijungti</a></p>
<p><a href="<?php echo base_url('users/publish'); ?>">Talpinti Skelbima</a></p>



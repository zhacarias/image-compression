<?php include('header.php'); ?>
    <div class="row cells1 container">
        <div class="cell">
            <div class="panel" id="drop_section">
                <div class="heading">
                    <span class="title">
                        <span class="mif-images"></span>
                        Image Compression
                        <span id="quota" class="place-right">Compressed images: <?php echo $quota; ?> / 500</span>
                    </span>
                </div>
                <div class="content">
                    <div id="dropzone_form" class="dropzone_frm">
                        <div class="dz-message" data-dz-message><span>Drag / Drop image here</span></div>
                    </div>
                    <div id="button_group" data-role="group" data-group-type="multi-state" data-button-style="class">
                        <button id="drop_download" class="button success block-shadow-success text-shadow" disabled>Download</button>
                        <button id="drop_clear" class="button danger block-shadow-danger text-shadow">Reset</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer bg-steel">
        <span class="footer-title">
            2017 <span class="mif-copyright"></span> SM Retail, Inc. - ITS HOBS Enterprise
        </span>
        <br><br>
        <span class="footer-credits">
            <span>Powered by TinyPNG</span>
        </span>
    </div>
<?php include('footer.php'); ?>



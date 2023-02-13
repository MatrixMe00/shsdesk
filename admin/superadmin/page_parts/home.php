<?php include_once("../../../includes/session.php");

    //add nav point session
    $_SESSION["nav_point"] = "Index";
?>
<section draggable="true">
    <div class="head">
        <div class="intro">
            <p>This menu is for the home page of the main website page. At this point, you decide on what to be shown and what not to be shown on it.</p>
        </div>
    </div>
</section>

<section draggable="true" class="section_main_block" id="active_carousel">
    <div class="head title_bar flex flex-space-content flex-center-align">
        <h3>Active Carousel</h3>
        <div class="close">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="body no_disp">
        <?php
            $result = $connect->query("SELECT * 
            FROM pageitemdisplays 
            WHERE active=TRUE AND item_page='home' AND item_type='carousel'");

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
        ?>
        <div class="item">
            <div class="top title_bar flex flex-space-content flex-center-align">
                <div class="flex flex-center-align flex-wrap">
                    <div class="image small_image">
                        <img src="<?php echo $url."/".$row['item_img']?>" alt="<?php echo $row['image_alt']?>">
                    </div>
                    <div class="content_title">
                        <h4><?php echo $row["item_head"]?></h4>
                    </div>
                </div>
                <div class="close">
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="middle">
                <div class="desc" contenteditable="false">
                    <p><?php echo html_entity_decode($row["item_desc"])?></p>
                </div>
                <?php if($row["item_button"] == "1" || $row["item_button"] == 1){?>
                <div class="content_button btn">
                    <button><?php echo $row["button_text"]?></button>
                </div>
                <?php }?>
            </div>
            <div class="edit no_disp">
                <span class="span_edit item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="edit" data-default-text="Edit">Edit</span>
                <span class="span_cancel item-event no_disp" data-item-event="cancel">Cancel</span>
                <span class="span_delete item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="delete" data-default-text="Delete">Delete</span>
                <span class="span_deactivate item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="deactivate" data-default-text="Deactivate">Deactivate</span>
            </div>
        </div>
        <?php
                }
            }else{
                echo "
                    <div class=\"item empty\">
                        <p>No items to display. Please add one to remove this message</p>
                    </div>
                ";
            }
        ?>
    </div>
</section>

<section draggable="true" class="section_main_block" id="inactive_carousel">
    <div class="head title_bar flex flex-space-content flex-center-align">
        <h3>Inactive Carousel</h3>
        <div class="close">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="body no_disp">
        <?php
            $result = $connect->query("SELECT * 
            FROM pageitemdisplays 
            WHERE active=FALSE AND item_page='home' AND item_type='carousel'");

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
        ?>
        <div class="item">
            <div class="top title_bar flex flex-space-content flex-center-align">
                <div class="flex flex-center-align flex-wrap">
                    <div class="image small_image">
                        <img src="<?php echo $url."/".$row['item_img']?>" alt="<?php echo $row['image_alt']?>">
                    </div>
                    <div class="content_title">
                        <h4><?php echo $row["item_head"]?></h4>
                    </div>
                </div>
                <div class="close">
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div class="middle">
                <div class="desc" contenteditable="false">
                    <p><?php echo html_entity_decode($row["item_desc"])?></p>
                </div>
                <?php if($row["item_button"] == "1" || $row["item_button"] == 1){?>
                <div class="content_button btn">
                    <button><?php echo $row["button_text"]?></button>
                </div>
                <?php }?>
            </div>
            <div class="edit no_disp">
                <span class="span_edit item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="edit" data-default-text="Edit">Edit</span>
                <span class="span_cancel item-event no_disp" data-item-event="cancel">Cancel</span>
                <span class="span_delete item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="delete" data-default-text="Delete">Delete</span>
                <span class="span_activate item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="activate" data-default-text="Activate">Activate</span>
            </div>
        </div>
        <?php
                }
            }else{
                echo "
                    <div class=\"item empty\">
                        <p>No items to display. Please add one to remove this message</p>
                    </div>
                ";
            }
        ?>
    </div>
</section>

<section draggable="true" class="section_main_block" id="add_carousel">
    <div class="head title_bar flex flex-space-content flex-center-align">
        <h3>Add a Carousel</h3>
        <div class="close">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="body no_disp" style="margin-top: 10px;">
        <form action="<?php echo $url?>/admin/superadmin/submit.php" method="POST" enctype="multipart/form-data" name="carouselForm">
            <p>This form is to add a carousel to the home page</p><br><br>
            <input type="hidden" name="item_page" value="home">
            <input type="hidden" name="item_type" value="carousel">

            <div class="body">
                <div class="joint">
                    <label for="item_img" class="file_label">
                        <span class="label_title">Please upload your cover background</span>
                        <div class="fore_file_display">
                            <input type="file" name="item_img" id="item_img" accept="image/*" required>
                            <span class="plus">+</span>
                            <span class="display_file_name">Choose or drag your file here</span>
                        </div>
                    </label>
                    <label for="display_avatar" class="no_disp">
                        <div id="display_avatar" class="display_image_box">
                            <img src="" alt="avatar">
                        </div>
                    </label>
                </div>
                <label for="image_alt">
                    <input type="text" name="image_alt" id="image_alt" placeholder="Please enter an alternate text for the image"
                    title="Alternate texts are texts that are seen when the image cannot be viewed by the user. They usually hold a very brief description about the image">
                </label>
                <label for="item_head">
                    <input type="text" name="item_head" id="item_head" placeholder="Provide a title for this block*" required>
                </label>
                <label for="item_desc" class="flex">
                    <textarea type="text" name="item_desc" id="item_desc" placeholder="Provide a  brief description about the carousel*" class="sadmin_tinymce"></textarea></textarea>
                </label>
                <div class="flex flex-center-align flex-wrap">
                    <label for="item_button" class="checkbox">
                        <input type="checkbox" name="item_button" id="item_button">
                        <span class="label_title" title="Use this option when you have a link to redirect the viewer">Add button to carousel</span>
                    </label>
                    <label for="button_text" class="no_disp" style="flex-direction: column;">
                        <p>Select a button</p>
                        <div class="flex btn flex-wrap">
                            <button type="button" name="button_text" value="Enter">Enter</button>
                            <button type="button" name="button_text" value="Explore">Explore</button>
                            <button type="button" name="button_text" value="Custom">Custom</button>
                            <input type="hidden" name="real_button_text">
                        </div>
                        
                    </label>
                    <label for="button_text_input" class="no_disp">
                        <input type="text" name="button_text" placeholder="Enter text here for custom button">
                    </label>
                    <label for="button_url" class="no_disp">
                        <input type="url" name="button_url" id="button_url" placeholder="Enter a url for this button">
                    </label> 
                </div>
            </div>
            <label for="activate" class="checkbox" title="This will make the carousel live immediately">
                <input type="checkbox" name="activate" id="activate">
                <span class="label_title">Activate this carousel upon publishing</span>
            </label>
            <label for="submit" class="btn btn_label">
                <button type="submit" name="submit" value="page_item_upload">Add Carousel</button>
            </label>
        </form>
    </div>
</section>

<script src="<?php echo $url?>/admin/superadmin/assets/scripts/page_parts/home.min.js?v=<?php echo time()?>" async></script>
<script src="<?php echo $url?>/admin/assets/scripts/tinymce/jquery.tinymce.min.js" async></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce/tinymce.min.js" async></script>
    <script src="<?php echo $url?>/admin/assets/scripts/tinymce.min.js?v=<?php echo time()?>" async></script>
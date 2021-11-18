<?php include_once("../../../includes/session.php")?>
<section>
    <div class="head">
        <div class="intro">
            <p>This menu is for the home page of the main website page. At this point, you decide on what to be shown and what not to be shown on it.</p>
        </div>
    </div>
</section>

<section class="section_main_block" id="active_carousel">
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
            FROM pageItemDisplays 
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
                    <p><?php echo $row["item_desc"]?></p>
                </div>
                <?php if($row["item_button"] == "1" || $row["item_button"] == 1){?>
                <div class="content_button btn">
                    <button><?php echo $row["button_text"]?></button>
                </div>
                <?php }?>
            </div>
            <div class="edit no_disp">
                <span class="span_edit item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="edit">Edit</span>
                <span class="span_delete item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="delete">Delete</span>
                <span class="span_deactivate item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="act">Deactivate</span>
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

<section class="section_main_block" id="inactive_carousel">
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
            FROM pageItemDisplays 
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
                    <p><?php echo $row["item_desc"]?></p>
                </div>
                <?php if($row["item_button"] == "1" || $row["item_button"] == 1){?>
                <div class="content_button btn">
                    <button><?php echo $row["button_text"]?></button>
                </div>
                <?php }?>
            </div>
            <div class="edit no_disp">
                <span class="span_edit item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="edit">Edit</span>
                <span class="span_delete item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="delete">Delete</span>
                <span class="span_activate item-event" data-item-id="<?php echo $row["id"]?>" data-item-event="act">Activate</span>
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

<section class="section_main_block" id="add_carousel">
    <div class="head title_bar flex flex-space-content flex-center-align">
        <h3>Add a Carousel</h3>
        <div class="close">
            <span></span>
            <span></span>
        </div>
    </div>
    <div class="body no_disp" style="margin-top: 10px;">
        <form action="<?php echo $url?>/admin/superadmin/submit.php" method="POST" enctype="multipart/form-data">
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
                    <textarea type="text" name="item_desc" id="item_desc" placeholder="Provide a  brief description about the carousel*" required></textarea>
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

<script>
    $(document).ready(function(){
        //activating a carousel
        $("span.span_activate, span.span_deactivate").click(function(){
            this_text = $(this).html();
            //change label before removing
            if(this_text == "Activate"){
                $(this).html("Deactivate");
            }else{
                $(this).html("Activate");
            }

            //get the parent elements
            parent = $(this).parents(".item");

            new_parent = "<div class=\"item\">";
            new_parent += $(parent).html();
            new_parent += "</div>";

            //now delete this parent
            $(parent).remove();

            //get the required root element
            if(this_text == "Activate"){
                root_parent = $("section#active_carousel").children(".body");
            }else{
                root_parent = $("section#inactive_carousel").children(".body");
            }

            //empty tabs
            empty = "<div class=\"item empty\">" + 
                    "<p>No items to display. Please add one to remove this message</p>" +
                    "</div>";

            //push new element into this new space
            $(root_parent).append(new_parent);
        })
    })
    //edit the carousel button
    $("input[name=item_button]").change(function(){
        check = $(this).prop("checked");

        if(check == true){
            $("label[for=button_text], label[for=button_url]").removeClass("no_disp");
        }else{
            $("label[for=button_text], label[for=button_url], label[for=button_text_input]").addClass("no_disp");

            //clear value of selected button
            $("input[name=real_button_text], input[name=button_text]").val("");

            //remove selected from all selected buttons
            $("button[name=button_text].selected").removeClass("selected");
        }
    })

    // $("form").submit(function(e){
    //     e.preventDefault();

    //     alert($(this).serialize());
    // })

    //enable the selected button
    $("button[name=button_text]").click(function(e){
        e.preventDefault(false);
        //get the text
        text = $(this).html();

        //remove selected from all selected buttons
        $("button[name=button_text].selected").removeClass("selected");

        $(this).addClass("selected");

        if(text != "Enter" && text != "Explore"){
            $("label[for=button_text_input]").removeClass("no_disp");
            $("input[name=button_text").focus();
        }else{
            $("label[for=button_text_input]").addClass("no_disp");

            //transfer value into real deal
            $("input[name=real_button_text]").val($(this).val());
        }
    })

    $("input[name=button_text]").blur(function(){
        //transfer value into real deal
        $("input[name=real_button_text]").val($(this).val());
    })

    //click the head of the carousel to show or hide body
    $(".section_main_block .head").click(function(){
        if($(this).siblings(".body").hasClass("no_disp")){
            $(".section_main_block>.body").addClass("no_disp");
            $(".section_main_block .head .close.clicked").removeClass("clicked");
            
            $(this).siblings(".body").removeClass("no_disp");
            $(this).children(".close").addClass("clicked");
        }else{
            $(this).siblings(".body").addClass("no_disp");
            $(this).children(".close").removeClass("clicked");
        }
    })

    //open or close the contents of a windowed tab
    $(".item .title_bar .close").click(function(){
        //close all other and reset all item buttons
        $(".section_main_block .body .item .middle").slideUp();
        $(".section_main_block .body .item .edit").addClass("no_disp");

        if($(this).parents(".item").hasClass("active")){
            //mark parents as inactive
            $(this).parents(".body").children(".item").removeClass("active");

            //send close to plus
            $(this).removeClass("clicked");
        }else{
            //remove active from open tabs
            $(".section_main_block .body .item.active").removeClass("active");

            //mark parent as active
            $(this).parents(".item").addClass("active");

            //send close to minus and declare this as opened
            $(".item .title_bar .close.clicked").removeClass("clicked");
            $(this).addClass("clicked");

            //get the middle and edit elements
            desc = $(this).parent().siblings(".middle");
            edit = $(this).parent().siblings(".edit");

            //display the middle class
            $(desc).slideDown();

            //switch the title of the close button
            title = $(this).attr("title");
            if(title == "Hide Details"){
                title = "Show Details";
            }else{
                title = "Hide Details";
            }

            $(this).prop("title", title);

            //show or hide the edit menu
            $(edit).toggleClass("no_disp");
        }
    })

    //edit a content
    $(".span_edit").click(function(){
        //check its html name and work with it
        html = $(this).html();

        if(html == "Edit"){
            //change to cancel
            $(this).html("Cancel");

            //add the edit feature to the description element
            $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",true).focus();
        }else if(html == "Save"){
            //step into the parent element
            parent = $(this).parents(".item");

            //when it is clicked as save, save everything and remake changes
            $(this).html("Edit");

            //remove the edit feature to the description element
            $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",false);
        }else{
            //when it is clicked as cancel
            $(this).html("Edit");

            //remove the edit feature to the description element
            $(this).parent().siblings(".middle").children(".desc").prop("contenteditable",false);
        }
    })

    $(".middle .desc").keypress(function(){
        //when a key is pressed, change the edit to save
        $(this).parent().siblings(".edit").children(".span_edit").html("Save");
    })

    //concerning the files that will be chosen
    $("input[type=file]").change(function(){
        //get the value of the image name
        image_path = $(this).val();

        //strip the path name to file name only
        image_name = image_path.split("C:\\fakepath\\");

        //store the name of the file into the display div
        if(image_path != ""){
            $(this).siblings(".plus").hide();
            $(this).siblings(".display_file_name").html(image_name);       
        }else{
            $(this).siblings(".plus").css("display","initial");
            $(this).siblings(".display_file_name").html("Choose or drag your file here");
        }
    })

    //the avatar of te school
    $("input[name=item_img]").change(function(){
        if($(this).val() != ''){
            //show the selected image
            $("label[for=display_avatar]").show();  

            //make the file ready for display
            var file = $("input[type=file]").get(0).files[0];

            if(file){
                //create a variable to make a read class instance
                reader = new FileReader();

                reader.onload = function(){
                    //pass the result to the image element
                    $("#display_avatar img").attr("src", reader.result);
                }

                //make the reading data a demo url
                reader.readAsDataURL(file);
            }
        }else{
            //hide the selected image
            $("label[for=display_avatar]").hide();

            //empty the image src
            $("#display_avatar img").prop("src", "");
        }
    })

    //making edits to a carousel
    $("span.item-event").click(function(){
        item_id = $(this).attr("data-item-id");
        item_event = $(this).attr("data-item-event");

        if(item_event == "edit"){

        }else if(item_event == "delete"){

        }else if(item_event == "act"){
            //check which activating key has been clicked
            act = $(this).html();

            //provide activation or deactivation key
            if(act == "Activate"){
                act = 0;
            }else{
                act = 1;
            }
        }
    })
</script>
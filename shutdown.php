<?php 
    //grabbing protocol
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

    //adding the domain name
    $domain_name = $_SERVER['HTTP_HOST'];

    //$url = $protocol.$domain_name;
    $url = $protocol.$domain_name;

    include("includes/appMemory.php");
    
    if($serverDown === false){
        $actual_path = str_replace("shutdown","", $actual_path);
        header("Location: $url$actual_path");
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SHSDesk | Under Construction</title>
        <style>
            body{
                min-height: 100vh; padding: 0; margin: 0;
                display: flex;
            }
            *{
                transition: all 0.5 ease-in-out;
            }

            .text{
                padding: 1rem; display: flex; flex-direction: column;
                flex: 2; height: calc(100vh - 2rem); justify-content: space-between;
                z-index: 1; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }
            .text p{font-size: large}
            .text h3{font-size: xx-large;}
            .head{font-size: large}
            .foot{
                display: flex; gap: 0.5em; align-self: center; flex-wrap: wrap; justify-content: center; align-items: center;
            }
            .foot-social{display: flex; gap: 1em; align-items: center}
            
            .image{
                flex: 3; height: 100vh;
            }
            .image img{
                width: 100%; object-fit: cover; height: inherit; object-position: center;
                filter: brightness(80%);
            }
            .icon{
                width: 1.7rem; height: 1.7rem;
            }
            .icon img{width: 100%; height: inherit}
            .bubble {
                position: fixed;
                z-index: 2;
                width: 10vw;
                height: 10vw;
                left: 20vw;
                background-color: #00bfff;
                border-radius: 50%;
                animation: bubbleAnimation 3s ease-in-out infinite;
            }

            .bubble:hover{
                animation-play-state: paused;
            }

            @keyframes bubbleAnimation {
                0% {
                    transform: translateY(-20px);
                }
                50% {
                    transform: translateY(5vh);
                }
                100% {
                    transform: translateY(-20px);
                }
            }
            @media screen and (min-width: 1020px){
                .text{
                    padding: 5rem; height: calc(100vh - 10rem);
                    flex: 3;
                }
                .image{ flex: 7}
            }
            @media screen and (max-width: 870px){
                .text, .image{flex: initial}
                .image{width: 100vw;}
                .image img{filter: brightness(50%)}
                .text{
                    background-color: rgba(100%,100%,100%, 0.85); position: fixed; align-self: center;
                    width: calc(80vw - 4rem); min-height: 40vh; max-height: 80vh; height: max-content;
                    left: 10vw; border-radius: 5px; padding: 1rem 2rem;
                }
                .bubble{
                    left: 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="bubble"></div>
        <div class="text">
            <div class="head">
                SHSDesk
            </div>
            <div class="middle">
                <h3>Under Construction</h3>
                <p>
                    Our website is currently undergoing a scheduled maintenance. We should be back shortly.
                    Thank you for your patience
                </p>
            </div>
            <div class="foot">
                <span>Stay in touch</span>
                <div class="foot-social">
                    <span class="icon" title="Whatsapp Us">
                        <img src="<?= "$url/assets/images/icons/push-outline.svg" ?>" alt="">
                    </span>
                    <span class="icon" title="Send an Email">
                        <img src="<?= "$url/assets/images/icons/mail-outline.svg" ?>" alt="">
                    </span>
                    <span class="icon" title="Call Us">
                        <img src="<?= "$url/assets/images/icons/phone-portrait-outline.svg" ?>" alt="">
                    </span>
                    
                </div>
            </div>
        </div>
        <div class="image">
            <img src="<?= $url ?>/assets/images/backgrounds/carousel/thought-catalog-xHaZ5BW9AY0-unsplash.jpg" alt="">
        </div>
    </body>
</html>
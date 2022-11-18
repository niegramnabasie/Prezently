
let visibleSection = document.getElementById('event_gifts');
let activMenuItem = document.getElementById('event_menu_gifts');

const gifts = document.getElementById('event_gifts');
const donations = document.getElementById('event_donations');
const share = document.getElementById('event_share');
const settings = document.getElementById('event_settings');

const event_menu_gifts = document.getElementById('event_menu_gifts');
event_menu_gifts.addEventListener('click', function displayGifts(){
    console.log('g');
    visibleSection.style.display = "none";
    activMenuItem.classList.remove("event_menu_item_active");

    gifts.style.display = "block";
    this.classList.add("event_menu_item_active");
    visibleSection = gifts;
    activMenuItem = this;
});

const event_menu_donations = document.getElementById('event_menu_donations');
event_menu_donations.addEventListener('click', function displayDonations(){
    console.log('d');
    visibleSection.style.display = "none";
    activMenuItem.classList.remove("event_menu_item_active");

    donations.style.display = "block";
    this.classList.add("event_menu_item_active");
    visibleSection=donations;
    activMenuItem = this;
});
const event_menu_share = document.getElementById('event_menu_share');
event_menu_share.addEventListener('click', function displayShare(){
    console.log('sh');
    visibleSection.style.display= 'none';
    console.log(visibleSection);
    activMenuItem.classList.remove("event_menu_item_active");

    share.style.display = "block";
    this.classList.add("event_menu_item_active");
    visibleSection=share;
    console.log(visibleSection);
    activMenuItem = this;
});
const event_menu_settings = document.getElementById('event_menu_settings');
event_menu_settings.addEventListener('click', function displaySettings(){
    console.log('se');
    visibleSection.style.display = "none";
    activMenuItem.classList.remove("event_menu_item_active");

    settings.style.display = "block";
    this.classList.add("event_menu_item_active");
    visibleSection=settings;
    activMenuItem = this;
});

// function displaySection() {
//     alert('123456');
    // if (section === 'gifts'){
    //     document.getElementById('event_gifts').style.display = "block";
    //     document.getElementById('event_donations').style.display = "none";
    //     document.getElementById('event_share').style.display = "none";
    //     document.getElementById('event_settings').style.display = "none";
    // } else if (section === 'donations'){
    //     document.getElementById('event_gifts').style.display = "none";
    //     document.getElementById('event_donations').style.display = "block";
    //     document.getElementById('event_share').style.display = "none";
    //     document.getElementById('event_settings').style.display = "none";
    // } else if (section === 'share'){
    //     document.getElementById('event_gifts').style.display = "none";
    //     document.getElementById('event_donations').style.display = "none";
    //     document.getElementById('event_share').style.display = "block";
    //     document.getElementById('event_settings').style.display = "none";
    // }else if (section === 'settings'){
    //     document.getElementById('event_gifts').style.display = "none";
    //     document.getElementById('event_donations').style.display = "none";
    //     document.getElementById('event_share').style.display = "none";
    //     document.getElementById('event_settings').style.display = "block";
    // }
// }
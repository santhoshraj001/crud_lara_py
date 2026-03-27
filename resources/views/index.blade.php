<!DOCTYPE html>
<html>
<head>
<!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet"> -->
<title>Employee CRUD</title>
</head>
<body>
<div class="form container">
<h2>Add Employee</h2>

<!-- <form action="/store" method="POST"> -->
    <form action="{{ isset($editdata) ? url('/update/'.$editdata->id) : url('/store') }}" method="POST" enctype="multipart/form-data">
@csrf
<div>
    <label for="name">Name</label>
<input type="text" name="name" placeholder="Name"
value="{{ isset($editdata) ? $editdata->name : '' }}">
</div>
<div>
    <label for="name">Image</label>
    <input type="file" name="images[]" multiple>
</div>
<div>
<label for="email">Email</label>
<input type="email" name="email" placeholder="Email"
value="{{ isset($editdata) ? $editdata->email : '' }}">
</div>
<div>
<label for="salary">Salary</label>
<input type="number" name="salary" placeholder="Salary"
value="{{ isset($editdata) ? $editdata->salary : '' }}">
</div>

<button type="submit" onclick="showMessage()">
{{ isset($editdata) ? 'Update' : 'Submit' }}
</button>
@if(isset($editdata))
<a href="/std">
    <button style="margin-top: 5px;" type="button">Cancel</button>
</a>
@endif
</form>
</div>
<hr>
<div class="calendar-container" style="font-size: smaller;">
    <p id="message" ></p>
<div id="calendar" style="max-width: 900px; margin: 40px auto; height: auto;"></div>
<div id="eventModal" class="modal">
    <div class="modal-content">
        <h3 id="modalTitle">Add Event</h3>

        <input type="hidden" id="event-id">

        <label>Title</label>
        <input type="text" id="event-title">

        <label>Time (HH:MM)</label>
        <input type="text" id="event-time">

        <label>AM / PM</label>
        <input type="text" id="event-period">

        <div class="modal-actions">
            <button id="saveBtn">Save</button>
            <button id="closeModal">Cancel</button>
        </div>
    </div>
</div>
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this event?</p>

        <div class="modal-actions">
            <button id="confirmYes">Yes</button>
            <button id="confirmNo">No</button>
        </div>
    </div>
</div>
</div>
<h2>Employee List</h2>

<table class="emp-table" border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Image</th>
<th>Email</th>
<th>Salary</th>
<th>Bonus</th>
<th>Tax</th>
<th>Grade</th>
<!-- <th>Event</th> -->
<th>Edit</th>
<th>Delete</th>
</tr>

@foreach($employees as $emp)

<tr>

<td>{{ $emp->id }}</td>
<td>{{ $emp->name }}</td>
<td>
    @php
        $images = json_decode($emp->images, true);
    @endphp

    @if(!empty($images))
        @foreach($images as $img)
        <div class="image-box">
            <img src="{{ asset('images/' . $img) }}" width="40" height="40">
        </div>
        @endforeach
    @else
        No Image
    @endif
</td>
<td>{{ $emp->email }}</td>
<td>{{ $emp->salary }}</td>
<td>{{ $emp->bonus }}</td>
<td>{{ $emp->tax }}</td>
<td>{{ $emp->grade }}</td>
<td><a href="/edit/{{ $emp->id }}">Edit</a></td>
<td><a href="/delete/{{ $emp->id }}">Delete</a></td>

</tr>

@endforeach

</table>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    
function showMessage(){
    alert("Employee form submitted or updated Successfully😊");
}

  function closeMenu(el){
    let box = el.closest('.event-box');
    // add force hide
    box.classList.add('force-hide');
    // remove it after mouse leaves (so hover works again later)
    box.addEventListener('mouseleave', function () {
        box.classList.remove('force-hide');
    }, { once: true });
}
document.addEventListener('DOMContentLoaded', function () {  //Run this code after full html load



let selectedDate = "";
let isEdit = false;
let deleteId = null;

    let calendarEl = document.getElementById('calendar');

    let calendar = new FullCalendar.Calendar(calendarEl, { //Create a full calendar instance

        initialView: 'dayGridMonth',   //show calendar in monthly view
        events: '/events',

        displayEventTime: true,

        // ✅ Custom event UI
        eventContent: function(arg) {   //this control how event look

            let time = "";

            if(arg.event.start){
                let date = new Date(arg.event.start);

                time = date.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }

            return {
                html: `
                <div class="event-box">
                    <span>${time} ${arg.event.title}</span>

                    <span class="menu-btn"
                        data-id="${arg.event.id}" 
                        data-date="${arg.event.startStr}"
                        data-title="${arg.event.title}">
                        🛠
                    </span>

                    <div class="menu-dropdown">
                        <div class="edit-btn" 
                            data-id="${arg.event.id}" 
                            data-date="${arg.event.startStr}"
                            data-title="${arg.event.title}">
                            Edit
                        </div>

                        <div class="delete-btn" 
                            data-id="${arg.event.id}">
                            Delete
                        </div>
                         <div class="cancel-btn" onclick="closeMenu(this)">Cancel</div>
                    </div>
                </div>
                `
            };
        },

        // ✅ ADD EVENT
        dateClick: function(info) {   //Run when user click on calendar
              isEdit = false;
    selectedDate = info.dateStr;
    document.getElementById('saveBtn').innerText = "Save";

    document.getElementById('modalTitle').innerText = "Add Event";
    document.getElementById('event-id').value = "";
    document.getElementById('event-title').value = "";
    document.getElementById('event-time').value = "";
    document.getElementById('event-period').value = "";

    document.getElementById('eventModal').style.display = "flex";

            // if (!/^([0]?\d|1[0-2]):([0-5]\d)$/.test(time)) {    //ensure time format like(09:30)
            // if (period === 'pm' && hours < 12) hours += 12;     //convert to 24-hour format
            // calendar.refetchEvents();                           //reload event from DB
            
        }

    });

    calendar.render();

    // ✅ SINGLE CLICK HANDLER (FIXED)
  document.addEventListener('click', function(e){                   //(e)=info of event  //addEventListener Listen for an event (like click) and run code


    document.querySelectorAll('.fc-daygrid-event').forEach(el => {   //querySelectorAll used to select all matched selector elements
        el.classList.remove('active-event');
    });

    // 🔹 DELETE
    if(e.target.classList.contains('delete-btn')){                    //Check if clicked element has class delete-btn 
 deleteId = e.target.getAttribute('data-id');
 document.getElementById('confirmModal').style.display = "flex"; 
        return;
    }
});
  // show modal
  document.getElementById('confirmYes').addEventListener('click', function(){

    if(!deleteId) return;

    $.ajax({
        url: "/delete-event",
        type: "POST",
        data: {
            id: deleteId,
            _token: "{{ csrf_token() }}"
        },
        success: function(){

            calendar.refetchEvents();

            // close modal
            document.getElementById('confirmModal').style.display = "none";

            // show message
            let msg = document.getElementById('message');
            msg.style.display = "block";
            msg.innerHTML = "Deleted successfully 😴";

            setTimeout(() => {
                msg.style.display = "none";
            }, 3000);

            // reset id
            deleteId = null;
        },
        error: function(xhr){
            console.log(xhr.responseText);
        }
    });

});
document.getElementById('confirmNo').addEventListener('click', function(){
    document.getElementById('confirmModal').style.display = "none";
    deleteId = null;
});
 
     // ✅ EDIT
     document.addEventListener('click', function(e){
    if(e.target.classList.contains('edit-btn')){    
        
            // let oldTitle = e.target.getAttribute('data-title');  //get value from html attribute

    isEdit = true;
    // change button text
    document.getElementById('saveBtn').innerText = "Update";

    let id = e.target.getAttribute('data-id');
    let title = e.target.getAttribute('data-title');
    let dateStr = e.target.getAttribute('data-date');

    selectedDate = dateStr.split("T")[0];

    let date = new Date(dateStr);

    let hours = date.getHours();
    let minutes = date.getMinutes();

    let period = hours >= 12 ? 'PM' : 'AM';
    if(hours > 12) hours -= 12;
    if(hours === 0) hours = 12;

    let time = String(hours).padStart(2,'0') + ":" + String(minutes).padStart(2,'0');

    // fill modal
    document.getElementById('modalTitle').innerText = "Edit Event";
    document.getElementById('event-id').value = id;
    document.getElementById('event-title').value = title;
    document.getElementById('event-time').value = time;
    document.getElementById('event-period').value = period;

    document.getElementById('eventModal').style.display = "flex";
        }

    });

    document.getElementById('saveBtn').addEventListener('click', function(){

    let id = document.getElementById('event-id').value;
    let title = document.getElementById('event-title').value;
    let time = document.getElementById('event-time').value;
    let period = document.getElementById('event-period').value.toLowerCase();

    if(!title || !time || !period){
        alert("All fields required");
        return;
    }

    let [hours, minutes] = time.split(':');
    hours = parseInt(hours);

    if (period === 'pm' && hours < 12) hours += 12;
    if (period === 'am' && hours === 12) hours = 0;

    let finalTime = String(hours).padStart(2,'0') + ":" + minutes;

    let start = selectedDate + " " + finalTime + ":00";

    let url = isEdit ? "/update-event" : "/store-event";

    let data = {
        title: title,
        start: start,
        _token: "{{ csrf_token() }}"
    };

    if(isEdit){
        data.id = id;
    }

    $.ajax({
        url: url,
        type: "POST",
        data: data,
        success: function(){
            calendar.refetchEvents();
            document.getElementById('eventModal').style.display = "none";
            // alert(isEdit ? "Updated" : "Added");
            let msgBox = document.getElementById('message');

            msgBox.innerText = isEdit ? "Event Updated Successfully 😊" : "Event Added Successfully 😊";
            msgBox.style.display = "block";
            // auto hide after 3 seconds
            setTimeout(() => {
              msgBox.style.display = "none";
            }, 3000);
        }
    });
});

document.getElementById('closeModal').addEventListener('click', function(){
    document.getElementById('eventModal').style.display = "none";
});

});

</script>
</body>
</html>
<style>
   /* Page Layout */
body{
    font-family: Arial, sans-serif;
    background-color:#f4f6f9;
    margin:0;
    padding:40px;
}

/* Form Container Center */
.form-container{
    display:flex;
    justify-content:center;
    margin-bottom:30px;
}

/* Heading */
h2{
    text-align:center;
    color:#333;
    font-family: system-ui;
    font-size: xx-large;
}

/* Form Box */
form{
    background-color: #98c3ca;
    
    padding:25px;
    width:320px;
    border-radius:45px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
    margin: auto;
    margin-bottom: 40px;
}

/* Label */
label{
    font-weight:bold;
    display:block;
    margin-bottom:5px;
}

/* Input Field */
input{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:4px;
    box-sizing:border-box;
}

/* Button */
button{
    width:100%;
    padding:10px;
    background:#3490dc;
    color:white;
    border:none;
    border-radius:20px;
    cursor:pointer;
    font-size:14px;
}

button:hover{
    background:#2779bd;
}

/* Table Styling */
.emp-table{
    width: max-content;
    border-collapse: collapse;
    background:white;
    margin-top:20px;
    margin-left: 13%;
}

/* Table Header */
th{
    background:#3490dc;
    color:white;
    padding:12px;
}

/* Table Cells */
td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

/* Zebra Rows */
tr:nth-child(even){
    background:#f2f2f2;
}

/* Links */
a{
    text-decoration:none;
    color:#3490dc;
    font-weight:bold;
}

a:hover{
    text-decoration:underline;
}
.image-box img {

    transition: transform 0.3s ease;
}

.image-box img:hover {
    transform: scale(2.0);
    cursor: pointer;
}
.calendar-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 15px;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.fc-toolbar-title {
    font-size: 22px;
    font-weight: bold;
}

.fc-button {
    background-color: #3490dc;
    border: none;
    border-radius: 6px;
}

.fc-button:hover {
    background-color: #2779bd;
}

.fc-daygrid-day-number {
    color: #333;
    font-weight: bold;
}

.fc-day-today {
    background: #e3f2fd;
}
.scroll-text {
    white-space: nowrap;
    overflow: hidden;
    display: block;
    position: relative;
}
 /* 🔹 Event container */

.event-box {
    position: relative;
    display: block;
}

/* 🔹 Menu button */
.menu-btn {
    float: right;
    cursor: pointer;
    color: #2964b7;
    margin-left: 4px;

    opacity: 0;
    transition: 0.2s;
}

.event-box:hover .menu-btn {
    opacity: 1;
}

/* 🔹 Dropdown */
.menu-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%; /* 👈 IMPORTANT (no gap) */

    background: #fff;
    border: 1px solid #ccc;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    border-radius: 4px;

    z-index: 10000;
}
/*-------------------*/
.event-box:hover .menu-dropdown {
    display: block;
}
/* 🔹 Dropdown items */
.menu-dropdown div {
    padding: 6px 12px;
    cursor: pointer;
    font-size: 13px;
}

.menu-dropdown div:hover {
    background: #f1f1f1;
}

/* 🔹 Fix overflow issue */
.fc-daygrid-event {
    overflow: visible;
    position: relative;
}

/* 🔹 Bring hovered event to front */
.fc-daygrid-event:hover {
    z-index: 9999;
}

/* 🔹 Smooth layering */
.fc-daygrid-event.fc-event {
    transition: z-index 0.2s ease;
}

/* 🔥 When closed manually, force hide */
.event-box.force-hide .menu-dropdown {
    display: none;
}
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    background: rgba(0,0,0,0.5);
    z-index: 20000;

    justify-content: center;
    align-items: center;
}

.modal-content {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    width: 300px;
}

.modal-content input {
    width: 100%;
    margin-bottom: 10px;
    padding: 6px;
}

.modal-actions {
    display: flex;
    justify-content: space-between;
}
#message{
    display: none;
    padding: 10px;
    margin: 10px 0;
    background: #d4edda;
    color: #155724;
    border-radius: 4px;
    font-size: 14px;
    width: fit-content;
    border-radius: 15px;
    border: 1px solid #c3e6cb;


}
</style>
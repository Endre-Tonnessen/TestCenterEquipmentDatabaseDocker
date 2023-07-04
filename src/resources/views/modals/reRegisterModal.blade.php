<!-- This modal is used when a user attempts to borrow an already borrowed item.
     The function of it is to ask the user if they want to re-register the current item in their own name.
-->


<!-- Button to request data from server. -->
<div style="display: none;">
    <form action="Borrow/re" method="POST" id="re-register">
        @csrf
        <input id="re-register_itemID" type="text" name="equipmentID" style="display: none;" value="">
        <input id="re-register_user_name" type="text" name="borrowName" style="display: none;" value="">
    </form>
    <button type="submit" name="re_register" form="re-register" id="re_registerButton"><h2>Borrow!</h2></button>
</div>



<script>
    // Open
    function openModal(BItemID, BName, BDate, newBorrower) {
        //Set data into the input fields
        document.querySelector('#re-register_itemID').setAttribute("value", BItemID);
        document.querySelector('#re-register_user_name').setAttribute("value", newBorrower);

        //Opens modal
        Swal.fire({
            icon: 'info',
            title: 'Do you want to re-register '+BItemID+'?',
            showDenyButton: true,
            denyButtonText: `No`,
            confirmButtonText: `Yes`,
            confirmButtonColor: 'green',
            button: ["No","Yes"],
            html: '<p><b>'+BItemID+'</b> is currently borrowed by <b>'+BName+'</b> on '+BDate+'</p><p>Do you want to register it on <b>'+newBorrower+'</b> instead?</p>',
        }).then((result) => {
            if (result.isConfirmed) {
                //Send to borrow page.
                document.querySelector('#re_registerButton').click(); //Click the button activating the 'formSenDItemID' form.
            }
        })
    }
</script>







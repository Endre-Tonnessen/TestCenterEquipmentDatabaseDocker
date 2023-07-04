
<script>
    /**
     * When a row in the table is clicked, user is promted if they wish to un-delete this item.
     * @param {*} e
     */
    function clickOnItem(e) {
        $("tr").click(function(event) {
            //Check that the clicked table is the correct one
            if ($(this).closest('tr').closest("#printDeletedItemsTable").length === 0) {
                return;
            }

            //itemID from clicked <tr>
            const itemID = $(this).closest('tr').find('td:first-child').html();
            document.querySelector('#unDelete').setAttribute('value', itemID);

            if (itemID == undefined) return; //Can't select title of item table.
            borrowModalSweetAlert(itemID)
        })
    }

    /**
     Promt asking user if they want to borrow the selected item.
     If yes --> User is sent to borrow.php, and the itemID is auto filled.
     If no  --> Promt closes.
     */
    function borrowModalSweetAlert(itemID) {
        Swal.fire({
            icon: 'question',
            title: 'Do you want to un-delete '+itemID+'?',
            showDenyButton: true,
            denyButtonText: `No`,
            confirmButtonText: `Yes`,
            confirmButtonColor: 'green',
            reverseButtons: true,
            html: '',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('unDeleteButton').click(); //Click the button activating the 'formSenDItemID' form.
            }
        })
    }
</script>

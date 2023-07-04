import {
    addCalibratinRangeAndAccuracy,
    addMeasuringRangeAndAccuracy, createEquipment,
    createRandomEquipmentName,
    loginAdmin
} from "./TestHelpers.cy";


describe('Equipment Page', () => {

    const name = createRandomEquipmentName()
    before(() => {

    })

    beforeEach(() => {
        cy.viewport(1920, 1080)
        cy.refreshDatabase();
        cy.seed();
        //cy.create('App\\Models\\Equipment', { equipmentID: name})
        loginAdmin('test@test.com', '12345678')
        createEquipment(name)
        cy.visit('/Equipment/'+name);
    })

    it('Changelog should have correct amount of dates ', function () {
        cy.get('#ChangeLogButton').click()
        //Should be two when just been created.
        cy.get('#VersionHistoryModal > .modal-dialog > .modal-content > .modal-body >').should('have.length', 2)
    });

    it('Changelog should have correct amount of dates 2', function () {
        cy.visit('/Equipment/'+name);

        //Should be 5, three changes, 2 for creating item.
        addCalibratinRangeAndAccuracy(name)
        addCalibratinRangeAndAccuracy(name)
        addCalibratinRangeAndAccuracy(name)

        cy.get('#ChangeLogButton').click()
        cy.get('.modal-body').children('div.col-12').its('length').should('eq',5)
    });

    it('equipment page displays', () => {
        cy.url().should('contain','Equipment')
        cy.contains(name);
    });

    it('Admins can access admin buttons', () => {
        cy.visit('/Equipment/'+name);

        cy.get('#genInfoModalTarget')
        cy.get("#updateNotes");
        cy.get("#updateCalInfo");
        cy.get("#addMeasuringButton");
        cy.get("#addCalibrationButton");
    });

    it('Successfully update equipment information', () => {
        cy.visit('/Equipment/'+name);
        cy.contains(name);

        //Input info
        cy.get('#Manufacturer').clear().type('Laerdal Medical AS')
        cy.get("#ModelNumber").clear().type("M98231")
        cy.get("#SerialNumber").clear().type("S42311")
        cy.get("#itemDescription").clear().type("Description")
        cy.get("#Department").clear().type("Shared Services")
        cy.get("#location").clear().type("LMAS Stavanger")
        cy.get("#Placement").clear().type("Shelf -100")
        cy.get("#Usage").clear().type("Usage")

        cy.get('#genInfoModalTarget').click()
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-footer > .btn-success').click()
        cy.contains("Sucessfully updated!")
        cy.get('.swal2-confirm').click()
        cy.reload()

        //Verify info was inserted and retrieved
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")
        cy.get("#SerialNumber").should('have.value',"S42311")
        cy.get("#itemDescription").should('have.value',"Description")
        cy.get("#Department").should('have.value',"Shared Services")
        cy.get("#location").should('have.value',"LMAS Stavanger")
        cy.get("#Placement").should('have.value',"Shelf -100")
        cy.get("#Usage").should('have.value',"Usage")
    });

    it('Add calibration range and accuracy', () => {
        //cy.contains("Add Calibration Range and Accuracy");
        addCalibratinRangeAndAccuracy(name)

        //Verify info was inserted.
        cy.get(':nth-child(1) > [style=""]').should('contain', '0')
        cy.get('tbody > :nth-child(1) > :nth-child(3)').should('contain', '100')
        cy.get('tbody > :nth-child(1) > :nth-child(3)').should('contain', '100') //Two times. Sometimes it misses the first one.
        cy.get('tbody > :nth-child(1) > :nth-child(4)').should('contain', 'mm')
        cy.get('tbody > :nth-child(1) > :nth-child(5)').should('contain', '+- 0.1 mm')
    });

    it('delete calibration range and accuracy row', function () {
        addCalibratinRangeAndAccuracy(name) //Add 3 remove 1.
        addCalibratinRangeAndAccuracy(name)
        addCalibratinRangeAndAccuracy(name)

        //Delete
        cy.get(':nth-child(2) > [style=" "]').click()
        cy.get('.swal2-confirm').click()
        cy.get('#CalibrationRangeAndAccuracyTable > tbody').find('tr').its('length').should('eq',2)
    });

    it('Add measuring range and accuracy', () => {
        addMeasuringRangeAndAccuracy(name)

        //Verify info was inserted.
        cy.get('#nav-profile-tab').click() //Click into correct tab
        cy.get('tbody > tr > :nth-child(2)').should('contain', '0')
        cy.get('tbody > tr > :nth-child(3)').should('contain', '100')
        cy.get('tbody > tr > :nth-child(4)').should('contain', 'mm')
        cy.get('tbody > tr > :nth-child(5)').should('contain', '+- 0.1 mm')
    });

    it('Remove measuring range and accuracy', function () {
        addMeasuringRangeAndAccuracy(name)

        cy.get('#nav-profile-tab').click() //Click into correct tab
        cy.get('tbody > tr > [style=" "]').click()
        cy.get('.swal2-confirm').click() //Delete row

        //Verify there are no rows
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr > .text-md-center')
        cy.get('#MeasurementRangeAndAccuracyTable > tbody').find('tr').its('length').should('eq',1)
    });

    it('Update Calibration Frequency area', function () {
        cy.visit('/Equipment/'+name);

        cy.get('#Cal_Interval_Year').type('1')
        cy.get('#Cal_Interval_Month').type('0')
        cy.get('#Last_Calibration_Date').type('2022-07-01')
        cy.get('#Calibration_Provider').type('IKM')
        cy.get('#Calibration_location').type('IKM, Oslo')
        cy.get('#Document_Reference').type('Document Agile ^29')

        cy.get('#updateCalInfo').click()
        cy.get('#calFreqModelTarget > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated calibration frequency")
        cy.get('#calFreqModelTarget > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated calibration frequency")
        cy.get('#calFreqModelTarget > .modal-dialog > .modal-content > .modal-footer > .btn-success').click()

        //Verify
        cy.get('#Cal_Interval_Year').should('have.value', '1')
        cy.get('#Cal_Interval_Month').should('have.value', '0')
        cy.get('#Last_Calibration_Date').should('have.value', '2022-07-01')
        cy.get('#Calibration_Provider').should('have.value', 'IKM')
        cy.get('#Calibration_location').should('have.value', 'IKM, Oslo')
        cy.get('#Document_Reference').should('have.value', 'Document Agile ^29')
        cy.get('#Next_Calibration_Date').should('have.value', '2023-07-01')
    });

    it('Update notes', function () {
        cy.visit('/Equipment/'+name);

        cy.get('#notes').type("The Low flow connection is used to measure small flow rates. In\n" +
            "order to calculate respiratory parameters using this\n" +
            "measurement channel, the trigger must be set to “infant” (>10.3\n" +
            "Standard Trigger Values). Thereby, the positive interface from\n" +
            "the differential pressure sensor will automatically be used as the\n" +
            "pressure sensor. The T-Piece with the connection tube can be\n" +
            "used to connect these two interfaces.")
        cy.get('#updateNotes').click()
        cy.get('#notesModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Added additional notes")
        cy.get('#notesModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Added additional notes")
        cy.get('#notesModal > .modal-dialog > .modal-content > .modal-footer > .btn-success').click()

        cy.get('#notes').should('contain.text', "The Low flow connection is used to measure small flow rates. In\n" +
            "order to calculate respiratory parameters using this\n" +
            "measurement channel, the trigger must be set to “infant” (>10.3\n" +
            "Standard Trigger Values). Thereby, the positive interface from\n" +
            "the differential pressure sensor will automatically be used as the\n" +
            "pressure sensor. The T-Piece with the connection tube can be\n" +
            "used to connect these two interfaces.")
    });

    it('Update image', function () {
        cy.visit('/Equipment/' + name);

        cy.get('.EquipmentImage').click()
        cy.get('#imgFile').selectFile("public/images/Laerdal_Logo_Small.jpg", {
            action: "select",
            force: true,
        })
        cy.get('.swal2-confirm').click()
        cy.get('#swal2-title').should('contain', "Updated image successfully!")
        cy.get('.swal2-confirm').click()
    });

    it('Changelog: Older versions should have correct information from requested date', function () {
        /* Go through, verify that fields are correct based on the date they where changed */
        cy.visit('/Equipment/'+name);

        // --- Input Changes ----
        cy.log("Input Changes")
        cy.get('#Manufacturer').clear().type('Laerdal Medical AS')
        cy.get('#genInfoModalTarget').click() //TODO: fix dis
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-footer > .btn-success').click()
        cy.get('.swal2-confirm').click()

        cy.get("#ModelNumber").clear().type("M98231")
        cy.get('#genInfoModalTarget').click()
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-body > .col-11 > .row > .col-md-10 > #ReasonText').clear().type("Updated general information")
        cy.get('#GeneralInfoModal > .modal-dialog > .modal-content > .modal-footer > .btn-success').click()
        cy.get('.swal2-confirm').click()

        addCalibratinRangeAndAccuracy(name)
        addCalibratinRangeAndAccuracy(name)
        addMeasuringRangeAndAccuracy(name)
        addMeasuringRangeAndAccuracy(name)
        // ----------------
        cy.get('#ChangeLogButton').click()
        cy.get('.modal-body').children('div.col-12').its('length').should('eq',8)

        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")

        //Verify
        //Oldest version should only have name field filled.
        cy.log("Should only have equipment id field filled")
        cy.get(':nth-child(8) > .text-decoration-none > .row > .col-md-8').click() //Oldest version, creation time.
        cy.get('#Manufacturer').should('have.value','')
        cy.get("#ModelNumber").should('have.value',"")
        cy.get('#itemID').should('have.value', name)

        //Manufacturer version
        cy.log("Manufacturer field should be filled")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(7) > .text-decoration-none > .row > .col-md-8').click()
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"")

        //Model number in next version //TODO: test this
        cy.log("Model number field should be filled")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(6) > .text-decoration-none > .row > .col-md-8').click()
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")

        //First calibration range should be added
        cy.log("First calibration range should be added")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(5) > .text-decoration-none > .row > .col-md-8').click()
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr > :nth-child(2)').should('contain', '0')
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr > :nth-child(3)').should('contain', '100')
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr > :nth-child(4)').should('contain', 'mm')
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr > :nth-child(5)').should('contain', '+- 0.1 mm')

        cy.log("Second calibration range should be added")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(4) > .text-decoration-none > .row > .col-md-8').click()
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr').should('have.length', 2)
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr').should('have.length', 1).should('contain', 'No data')
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")

        cy.log("First measuring range should be added")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(3) > .text-decoration-none > .row > .col-md-8').click()
        //New row
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr > :nth-child(2)').should('contain', '0')
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr > :nth-child(3)').should('contain', '100')
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr > :nth-child(4)').should('contain', 'mm')
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr > :nth-child(5)').should('contain', '+- 0.1 mm')
        //Old data should still be there
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr').should('have.length', 2)
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr').should('have.length', 1)
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")

        cy.log("Second measuring range should be added")
        cy.get('#ChangeLogButton').click()
        cy.get(':nth-child(2) > .text-decoration-none > .row > .col-md-8').click()
        cy.get('#CalibrationRangeAndAccuracyTable > tbody > tr').should('have.length', 2)
        cy.get('#MeasurementRangeAndAccuracyTable > tbody > tr').should('have.length', 2)
        cy.get('#Manufacturer').should('have.value','Laerdal Medical AS')
        cy.get("#ModelNumber").should('have.value',"M98231")
    });

});




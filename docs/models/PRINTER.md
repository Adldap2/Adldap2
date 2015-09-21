# Printer

The printer model will be returned when an AD result entry contains the object category: `print-queue`.

##### Getting the printers `printername` attribute
    
    // Returns the printers name.
    $printer->getPrinterName();

##### Getting the printers `printsharename` attribute

    // Returns the printers share name.
    $printer->getPrinterShareName();

##### Getting the printers `printmemory` attribute

    // Returns the printers memory.
    $printer->getMemory();


##### Getting the printers `url` attribute

    // Returns the printers URL.
    $printer->getUrl();


##### Getting the printers `location` attribute

    // Returns the printers location.
    $printer->getLocation();

##### Getting the printers `servername` attribute

    // Returns the server name that the current printer is connected to.
    $printer->getServerName();

##### Getting the printers `printcolor` attribute

    // Returns true / false if the printer can print in color.
    $printer->getColorSupported();

##### Getting the printers `printduplexsupported` attribute

    // Returns true / false if the printer supports duplex printing.
    $printer->getDuplexSupported();

##### Getting the printers `printmediasupported` attribute

    // Returns an array of printer paper types that the printer supports.
    $printer->getMediaSupported();

##### Getting the printers `printstaplingsupported` attribute

    // Returns true / false if the printer supports stapling.
    $printer->getStaplingSupported();

##### Getting the printers `printbinnames` attribute

    // Returns an array of the printers bin names.
    $printer->getPrintBinNames();
    
##### Getting the printers `printmaxresolutionsupported` attribute

    // Returns the printers maximum resolution.
    $printer->getPrintMaxResolution();

##### Getting the printers `printorientationssupported` attribute

    // Returns the printers orientations supported.
    $printer->getPrintOrientations();

##### Getting the printers `drivername` attribute

    // Returns the driver name of the printer.
    $printer->getDriverName();

##### Getting the printers `driverversion` attribute

    // Returns the printer drivers version number.
    $printer->getDriverVersion();

##### Getting the printers `priority` attribute

    // Returns the priority number of the printer.
    $printer->getPriority();

##### Getting the printers `printstarttime` attribute

    // Returns the printers start time.
    $printer->getPrintStartTime();

##### Getting the printers `printendtime` attribute

    // Returns the printers end time.
    $printer->getPrintEndTime();

##### Getting the printers `portname` attribute

    // Returns the port name of printer.
    $printer->getPortName();

##### Getting the printers `versionnumber` attribute

    // Returns the printers version number.
    $printer->getVersionNumber();

##### Getting the printers `printrate` attribute

    // Returns the print rate.
    $printer->getPrintRate();

##### Getting the printers `printrateunit` attribute

    // Returns the print rate unit.
    $printer->getPrintRate();

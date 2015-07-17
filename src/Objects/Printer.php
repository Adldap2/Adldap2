<?php

namespace Adldap\Objects;

use Adldap\Objects\Ldap\Entry;

class Printer extends Entry
{
    /**
     * Returns the printers name.
     *
     * @return string
     */
    public function getPrinterName()
    {
        return $this->getAttribute('printername', 0);
    }

    /**
     * Returns the printers share name.
     *
     * @return string
     */
    public function getPrinterShareName()
    {
        return $this->getAttribute('printsharename', 0);
    }

    /**
     * Returns the printers memory.
     *
     * @return string
     */
    public function getMemory()
    {
        return $this->getAttribute('printmemory', 0);
    }

    /**
     * Returns the printers URL.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->getAttribute('url', 0);
    }

    /**
     * Returns the printers location.
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getAttribute('location', 0);
    }

    /**
     * Returns the server name that the
     * current printer is connected to.
     *
     * @return string
     */
    public function getServerName()
    {
        return $this->getAttribute('servername', 0);
    }

    /**
     * Returns true / false if the printer can print in color.
     *
     * @return null|bool
     */
    public function getColorSupported()
    {
        return $this->convertStringToBool($this->getAttribute('printcolor', 0));
    }

    /**
     * Returns true / false if the printer supports duplex printing.
     *
     * @return null|bool
     */
    public function getDuplexSupported()
    {
        return $this->convertStringToBool($this->getAttribute('printduplexsupported', 0));
    }

    /**
     * Returns an array of printer paper types that the printer supports.
     *
     * @return array
     */
    public function getMediaSupported()
    {
        return $this->getAttribute('printmediasupported');
    }

    /**
     * Returns true / false if the printer supports stapling.
     *
     * @return null|bool
     */
    public function getStaplingSupported()
    {
        return $this->convertStringToBool($this->getAttribute('printstaplingsupported', 0));
    }

    /**
     * Returns an array of the printers bin names.
     *
     * @return array
     */
    public function getPrintBinNames()
    {
        return $this->getAttribute('printbinnames');
    }

    /**
     * Returns the printers maximum resolution.
     *
     * @return string
     */
    public function getPrintMaxResolution()
    {
        return $this->getAttribute('printmaxresolutionsupported', 0);
    }

    /**
     * Returns the printers orientations supported.
     *
     * @return string
     */
    public function getPrintOrientations()
    {
        return $this->getAttribute('printorientationssupported', 0);
    }

    /**
     * Returns the driver name of the printer.
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->getAttribute('drivername', 0);
    }

    /**
     * Returns the printer drivers version number.
     *
     * @return string
     */
    public function getDriverVersion()
    {
        return $this->getAttribute('driverversion', 0);
    }

    /**
     * Returns the priority number of the printer.
     *
     * @return string
     */
    public function getPriority()
    {
        return $this->getAttribute('priority', 0);
    }

    /**
     * Returns the printers start time.
     *
     * @return string
     */
    public function getPrintStartTime()
    {
        return $this->getAttribute('printstarttime', 0);
    }

    /**
     * Returns the printers end time.
     *
     * @return string
     */
    public function getPrintEndTime()
    {
        return $this->getAttribute('printendtime', 0);
    }

    /**
     * Returns the port name of printer.
     *
     * @return string
     */
    public function getPortName()
    {
        return $this->getAttribute('portname', 0);
    }

    /**
     * Returns the printers version number.
     *
     * @return string
     */
    public function getVersionNumber()
    {
        return $this->getAttribute('versionnumber', 0);
    }

    /**
     * Returns the print rate.
     *
     * @return string
     */
    public function getPrintRate()
    {
        return $this->getAttribute('printrate', 0);
    }

    /**
     * Returns the print rate unit.
     *
     * @return string
     */
    public function getPrintRateUnit()
    {
        return $this->getAttribute('printrateunit', 0);
    }
}

<?php

namespace App\Services;

/*
    Created on March 2020
    Translated from python to php on April 2020

    Various meteorological and related functions

    @author: Cor
    @translator: Julien Kenneth Pleijte
*/

use DateTime;
use DateTimeZone;
use test\Mockery\MockingNullableMethodsTest;

/**
 * Class PETService
 * @package App\Services
 */
class PETService {

    #*************************************************************************************
    # Block 1:
    # Solar radiation functions for first-order estimates in practical applications

    public function fr_diffuse($solar_down, float $lat = 52., float $lon = 5.1, int $DOY = 180, float $utc_dec = 11.66) {
        /* Estimate fraction of diffuse solar radiation according to Erbs et al. (1982) */

        $trans = $this->transmissivity($solar_down, $lat, $lon, $DOY, $utc_dec);
        if ($trans <= 0.22) {
            $frdif = 1. - 0.09 * $trans;
        }
        else if ($trans <= 0.80) {
            $frdif = 0.9511 - 0.1604 * $trans + 4.388 * $trans ** 2 - 16.638 * $trans ** 3 + 12.336 * $trans ** 4;
        }
        else {
            $frdif = 0.165;
        }
        return $frdif;
    }


    public function transmissivity($solar_down, $lat = 52., $lon = 5.1, $DOY = 180, $utc_dec = 11.66) {
        /* Estimate atmospheric transmissivity according to De Rooy and Holtslag (1999) */

        return $solar_down / (1367. * $this->sin_solar_elev($lat, $lon, $DOY, $utc_dec));
    }


    public function sin_solar_elev(float $lat = 52., float $lon = 5.1, int $DOY = 171, float $utc_dec = 11.66) {
        /*
           Sine of solar elevation as well as cosine of zenith angle for a given
           location (lat,lon) and time (UTC)
           Note: functionault De Bilt (Netherlands), 11.66 UTC on day-of-year 171
        */

        $fac1 = sin($this->solar_decl($DOY)) * sin(pi() * $lat / 180.);
        $fac2 = cos($this->hour_angle($lon, $utc_dec)) * cos($this->solar_decl($DOY)) * cos(pi() * $lat / 180);
        return ($fac1 + $fac2);
    }

    public function solar_decl(int $DOY = 180) {
        /* Solar declination on a given day-of-year. */

        return 0.409 * cos(2. * pi() * ($DOY - 171) / 365.25);
    }


    public function hour_angle(float $lon = 5.1, float $utc_dec = 11.66) {
        /* Approximate solar hour angle in radians */

        return pi() * ($utc_dec / 12. + $lon / 180. - 1.);
    }

    public function solar_clear(float $lon = 5.1, float $lat = 52., int $DOY = 171, float $utc_dec = 11.66) {
        /*
            Expected solar radiation for clear weather conditions using
            parameters fo De Bilt, NL, according to De Rooy and Holtslag (1999)
        */

        return max(1041. * $this->sin_solar_elev($lat, $lon, $DOY, $utc_dec) - 69., 0.);
    }

    #*************************************************************************************
    # Block 2:
    # Temperature and humidity related functions

    public function T_Kel($Ta) {
        /* Convert (air) temperature to absolute temperature in Kelvin */

        if ($Ta <= 150.) {
            $Tk = $Ta + 273.15;
        }
        else {
            $Tk = $Ta;
        }
        return ($Tk);
    }

    public function T_Cel($Ta) {
        /* Convert (air) temperature to temperature in Celsius */

        if ($Ta <= 150.) {
            $Tc = $Ta;
        }
        else {
            $Tc = $Ta - 273.15;
        }
        return ($Tc);
    }


    public function Emis_atm($Ta, $RH) {
        /* Estimate atmospheric emissivity */

        $ep = 0.01 * $RH * $this->es($Ta);
        $emis_atm = 0.575 * $ep ** 0.143;
        return ($emis_atm);
    }

    public function Lvap($Ta) {
        /* Latent heat of vaporisation */

        $Lv = 2.46 * pow(10.0, 6.0) * $Ta / $Ta; # Fixed value for the time being
        return ($Lv);
    }

    public function es($Ta) {
        /*
            Saturation vapour pressure of sweet water
            Notes:
            - Ta can be given in K (>150K) or °C (<150°C).
            - Not valid over ice
        */

        $Tk = $this->T_Kel($Ta);
        $y = ($Tk - 273.15)/($Tk - 32.18);
        $es = 1.004 * 6.1121 * exp(17.502 * $y);

        return $es;
    }

    public function T_dew1($ew) {
        /*
            Dewpoint temperature in °C from vapour pressure over sweet water
            Notes:
            - e must be given in hPa
            - Not valid over ice
        */

        $z = log($ew / (6.1121*1.004));
        $td = 240.97 * $z / (17.502 - $z);

        return $td;
    }

    public function T_dew2($Ta, $RH) {
        /*
            Dewpoint temperature in °C from air temperature and relative humidity
            Notes:
            - Function depends on public function es
            - Ta can be given in °C or K; RH in percentage (>1) or as a fraction (<=1)
            - Not valid over ice
        */

        if ($RH > 1) {
            $hfrac = $RH / 100.;
        }
        else {
            $hfrac = $RH;
        }

        $ew = $hfrac * $this->es($Ta);

        $z = log($ew / (6.1121 * 1.004));
        $td = 240.97 * $z / (17.502 - $z);

        return $td;
    }

    public function T_wb($Ta, $RH) {
        /*
            Estimation of Wetbulb temperature in °C from air temperature and relative humidity,
            according to Stull (2011).
            Notes:
            - Ta can be given in °C or K; RH in percentage (>1) or as a fraction (<=1)
            - See Stull (2011) for limitations
        */

        $Tc = $this->T_Cel($Ta);
        if ($RH <= 1) {
            $RH = $RH * 100.;
        }

        $fac1 = $Tc * atan(0.151977 * ($RH + 8.313659) ** (0.5));
        $fac2 = atan($Tc + $RH) - atan($RH - 1.676331);
        $fac3 = 0.00391838 * ($RH) ** (3/2) * atan(0.023101 * $RH) - 4.686035;
        $Twb = $fac1 + $fac2 + $fac3;

        return $Twb;
    }

    #*************************************************************************************
    # Block 3:
    # Thermal comfort functions

    public function calc_Tglobe($Ta, $RH, $Ua, $Solar, $fdir, $cza, $Pa = 1013.25) {
        /*
           Estimate globe temperature according to Liljegren et al. (2008)
           Notes:
               - Result is returned as temperature in °C
               - Provide pressure Pa in hPa
        */

        if ($cza < 0.) {
            if ($Solar > 0.) {
                $Solar = 0.;
            }
        }

        $Tair = $this->T_Kel($Ta);
        $sigma = 5.6696e-8;
        $emis_air = $this->Emis_atm($Tair, $RH);
        $emis_globe = 0.95;
        $alb_globe = 0.05;
        $d_globe = 0.0508;
        $emis_sfc = 0.999;
        $alb_sfc = 0.45;
        $convergence = 0.02;
        $max_iter = 50;
        $Tsfc = $Tair;
        $Tglobe_prev = $Tair;
        $Converged = False;
        $iter = 0;
        while (!$Converged and $iter <= $max_iter) {
            $iter = $iter + 1;
            $Tref = 0.5 * ($Tglobe_prev + $Tair); # evaluate properties at the average temperature
            $h = $this->h_sphere_in_air($d_globe, $Tref, $Pa, $Ua);
            $Tglobe = (0.5 * ($emis_air * $Tair ** 4 + $emis_sfc * $Tsfc ** 4) - $h / ($emis_globe * $sigma) * ($Tglobe_prev - $Tair) + $Solar / (2 * $emis_globe * $sigma) * (1. - $alb_globe) * ($fdir * (1. / (2. * $cza) - 1.) + 1. + $alb_sfc)) ** 0.25;
            if (abs($Tglobe - $Tglobe_prev) < $convergence) {
                $Converged = True;
            } else {
                $Tglobe_prev = (0.9 * $Tglobe_prev + 0.1 * $Tglobe);
            }
        }

        if ($Converged) {
            $Tglobe = $this->T_Cel($Tglobe);
        }
        else {
            $Tglobe = NAN;
        }

        return($Tglobe);
    }

    public function Tmrt($Tglobe, $Ta, $Ua) {
        /*
            Approximate mean radiant temperature using the fit recommended
            by Thorsson et al. (2007)
        */

        $diam = 150.0;
        $emis = 1.0;
        $ua_min = 0.13;
        $a = ($Tglobe + 273.15) ** 4;
        $b = 1.1 * 10 ** 8 * max($Ua, $ua_min) ** 0.6 / ($emis * $diam ** 0.4) * ($Tglobe - $Ta);
        $Tmrt = ($a + $b) ** 0.25 - 273.15;
        return($Tmrt);
    }

    public function WBGT($Tglobe, $Ta, $Twb) {
        /*
            Web Bulb Globe Temperature
            Note:
            - Basically valid for outdoor conditions. When solar radiation can be neglected
            use Ta = Tglobe (e.g. indoors)
        */

        $WBGT = 0.7 * $this->T_Cel($Twb) + 0.2 * $this->T_Cel($Tglobe) + 0.1 * $this->T_Cel($Ta);

        return $WBGT;
    }

    public function UTCI(float $Ta, float $mrt, float $Ua = 0.5, float $RH = 60.) {
        /* Approximate UTCI using the polynomial fit provided at www.UTCI.org */

        # Do a series of checks to be sure that the input values are within
        # the bounds accepted by the model.
        $check = ($Ta > -50.0 and $Ta < 50.0 and
            $mrt - $Ta > -30.0 and $mrt - $Ta < 70.0);
        if ($Ua < 0.5) {
            $Ua = 0.5;
        }
        else if ($Ua > 17) {
            $Ua = 17;
        }

        # If everything is good, run the data through the model below to get
        # the UTCI.
        # This is a python version of the UTCI_approx function
        # Version a 0.002, October 2009
        # Ta: air temperature, degrees Celsius
        # ehPa: water vapour presure, hPa=hecto Pascal
        # Tmrt: mean radiant temperature, degrees Celsius
        # Ua: wind speed 10m above ground level in m/s

        if ($check) {
            $ehPa = $this->es($Ta) * ($RH / 100.0);
            $D_Tmrt = $mrt - $Ta;
            $Pa = $ehPa / 10.0;  # convert vapour pressure to kPa

            $UTCI_approx = ($Ta +
                (0.607562052) +
                (-0.0227712343) * $Ta +
                (8.06470249 * (10 ** (-4))) * $Ta * $Ta +
                (-1.54271372 * (10 ** (-4))) * $Ta * $Ta * $Ta +
                (-3.24651735 * (10 ** (-6))) * $Ta * $Ta * $Ta * $Ta +
                (7.32602852 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ta * $Ta +
                (1.35959073 * (10 ** (-9))) * $Ta * $Ta * $Ta * $Ta * $Ta * $Ta +
                (-2.25836520) * $Ua +
                (0.0880326035) * $Ta * $Ua +
                (0.00216844454) * $Ta * $Ta * $Ua +
                (-1.53347087 * (10 ** (-5))) * $Ta * $Ta * $Ta * $Ua +
                (-5.72983704 * (10 ** (-7))) * $Ta * $Ta * $Ta * $Ta * $Ua +
                (-2.55090145 * (10 ** (-9))) * $Ta * $Ta * $Ta * $Ta * $Ta * $Ua +
                (-0.751269505) * $Ua * $Ua +
                (-0.00408350271) * $Ta * $Ua * $Ua +
                (-5.21670675 * (10 ** (-5))) * $Ta * $Ta * $Ua * $Ua +
                (1.94544667 * (10 ** (-6))) * $Ta * $Ta * $Ta * $Ua * $Ua +
                (1.14099531 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ta * $Ua * $Ua +
                (0.158137256) * $Ua * $Ua * $Ua +
                (-6.57263143 * (10 ** (-5))) * $Ta * $Ua * $Ua * $Ua +
                (2.22697524 * (10 ** (-7))) * $Ta * $Ta * $Ua * $Ua * $Ua +
                (-4.16117031 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ua * $Ua * $Ua +
                (-0.0127762753) * $Ua * $Ua * $Ua * $Ua +
                (9.66891875 * (10 ** (-6))) * $Ta * $Ua * $Ua * $Ua * $Ua +
                (2.52785852 * (10 ** (-9))) * $Ta * $Ta * $Ua * $Ua * $Ua * $Ua +
                (4.56306672 * (10 ** (-4))) * $Ua * $Ua * $Ua * $Ua * $Ua +
                (-1.74202546 * (10 ** (-7))) * $Ta * $Ua * $Ua * $Ua * $Ua * $Ua +
                (-5.91491269 * (10 ** (-6))) * $Ua * $Ua * $Ua * $Ua * $Ua * $Ua +
                (0.398374029) * $D_Tmrt +
                (1.83945314 * (10 ** (-4))) * $Ta * $D_Tmrt +
                (-1.73754510 * (10 ** (-4))) * $Ta * $Ta * $D_Tmrt +
                (-7.60781159 * (10 ** (-7))) * $Ta * $Ta * $Ta * $D_Tmrt +
                (3.77830287 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ta * $D_Tmrt +
                (5.43079673 * (10 ** (-10))) * $Ta * $Ta * $Ta * $Ta * $Ta * $D_Tmrt +
                (-0.0200518269) * $Ua * $D_Tmrt +
                (8.92859837 * (10 ** (-4))) * $Ta * $Ua * $D_Tmrt +
                (3.45433048 * (10 ** (-6))) * $Ta * $Ta * $Ua * $D_Tmrt +
                (-3.77925774 * (10 ** (-7))) * $Ta * $Ta * $Ta * $Ua * $D_Tmrt +
                (-1.69699377 * (10 ** (-9))) * $Ta * $Ta * $Ta * $Ta * $Ua * $D_Tmrt +
                (1.69992415 * (10 ** (-4))) * $Ua * $Ua * $D_Tmrt +
                (-4.99204314 * (10 ** (-5))) * $Ta * $Ua * $Ua * $D_Tmrt +
                (2.47417178 * (10 ** (-7))) * $Ta * $Ta * $Ua * $Ua * $D_Tmrt +
                (1.07596466 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ua * $Ua * $D_Tmrt +
                (8.49242932 * (10 ** (-5))) * $Ua * $Ua * $Ua * $D_Tmrt +
                (1.35191328 * (10 ** (-6))) * $Ta * $Ua * $Ua * $Ua * $D_Tmrt +
                (-6.21531254 * (10 ** (-9))) * $Ta * $Ta * $Ua * $Ua * $Ua * $D_Tmrt +
                (-4.99410301 * (10 ** (-6))) * $Ua * $Ua * $Ua * $Ua * $D_Tmrt +
                (-1.89489258 * (10 ** (-8))) * $Ta * $Ua * $Ua * $Ua * $Ua * $D_Tmrt +
                (8.15300114 * (10 ** (-8))) * $Ua * $Ua * $Ua * $Ua * $Ua * $D_Tmrt +
                (7.55043090 * (10 ** (-4))) * $D_Tmrt * $D_Tmrt +
                (-5.65095215 * (10 ** (-5))) * $Ta * $D_Tmrt * $D_Tmrt +
                (-4.52166564 * (10 ** (-7))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt +
                (2.46688878 * (10 ** (-8))) * $Ta * $Ta * $Ta * $D_Tmrt * $D_Tmrt +
                (2.42674348 * (10 ** (-10))) * $Ta * $Ta * $Ta * $Ta * $D_Tmrt * $D_Tmrt +
                (1.54547250 * (10 ** (-4))) * $Ua * $D_Tmrt * $D_Tmrt +
                (5.24110970 * (10 ** (-6))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt +
                (-8.75874982 * (10 ** (-8))) * $Ta * $Ta * $Ua * $D_Tmrt * $D_Tmrt +
                (-1.50743064 * (10 ** (-9))) * $Ta * $Ta * $Ta * $Ua * $D_Tmrt * $D_Tmrt +
                (-1.56236307 * (10 ** (-5))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (-1.33895614 * (10 ** (-7))) * $Ta * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (2.49709824 * (10 ** (-9))) * $Ta * $Ta * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (6.51711721 * (10 ** (-7))) * $Ua * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (1.94960053 * (10 ** (-9))) * $Ta * $Ua * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (-1.00361113 * (10 ** (-8))) * $Ua * $Ua * $Ua * $Ua * $D_Tmrt * $D_Tmrt +
                (-1.21206673 * (10 ** (-5))) * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-2.18203660 * (10 ** (-7))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (7.51269482 * (10 ** (-9))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (9.79063848 * (10 ** (-11))) * $Ta * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (1.25006734 * (10 ** (-6))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-1.81584736 * (10 ** (-9))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-3.52197671 * (10 ** (-10))) * $Ta * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-3.36514630 * (10 ** (-8))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (1.35908359 * (10 ** (-10))) * $Ta * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (4.17032620 * (10 ** (-10))) * $Ua * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-1.30369025 * (10 ** (-9))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (4.13908461 * (10 ** (-10))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (9.22652254 * (10 ** (-12))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-5.08220384 * (10 ** (-9))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-2.24730961 * (10 ** (-11))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (1.17139133 * (10 ** (-10))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (6.62154879 * (10 ** (-10))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (4.03863260 * (10 ** (-13))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (1.95087203 * (10 ** (-12))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (-4.73602469 * (10 ** (-12))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt +
                (5.12733497) * $Pa +
                (-0.312788561) * $Ta * $Pa +
                (-0.0196701861) * $Ta * $Ta * $Pa +
                (9.99690870 * (10 ** (-4))) * $Ta * $Ta * $Ta * $Pa +
                (9.51738512 * (10 ** (-6))) * $Ta * $Ta * $Ta * $Ta * $Pa +
                (-4.66426341 * (10 ** (-7))) * $Ta * $Ta * $Ta * $Ta * $Ta * $Pa +
                (0.548050612) * $Ua * $Pa +
                (-0.00330552823) * $Ta * $Ua * $Pa +
                (-0.00164119440) * $Ta * $Ta * $Ua * $Pa +
                (-5.16670694 * (10 ** (-6))) * $Ta * $Ta * $Ta * $Ua * $Pa +
                (9.52692432 * (10 ** (-7))) * $Ta * $Ta * $Ta * $Ta * $Ua * $Pa +
                (-0.0429223622) * $Ua * $Ua * $Pa +
                (0.00500845667) * $Ta * $Ua * $Ua * $Pa +
                (1.00601257 * (10 ** (-6))) * $Ta * $Ta * $Ua * $Ua * $Pa +
                (-1.81748644 * (10 ** (-6))) * $Ta * $Ta * $Ta * $Ua * $Ua * $Pa +
                (-1.25813502 * (10 ** (-3))) * $Ua * $Ua * $Ua * $Pa +
                (-1.79330391 * (10 ** (-4))) * $Ta * $Ua * $Ua * $Ua * $Pa +
                (2.34994441 * (10 ** (-6))) * $Ta * $Ta * $Ua * $Ua * $Ua * $Pa +
                (1.29735808 * (10 ** (-4))) * $Ua * $Ua * $Ua * $Ua * $Pa +
                (1.29064870 * (10 ** (-6))) * $Ta * $Ua * $Ua * $Ua * $Ua * $Pa +
                (-2.28558686 * (10 ** (-6))) * $Ua * $Ua * $Ua * $Ua * $Ua * $Pa +
                (-0.0369476348) * $D_Tmrt * $Pa +
                (0.00162325322) * $Ta * $D_Tmrt * $Pa +
                (-3.14279680 * (10 ** (-5))) * $Ta * $Ta * $D_Tmrt * $Pa +
                (2.59835559 * (10 ** (-6))) * $Ta * $Ta * $Ta * $D_Tmrt * $Pa +
                (-4.77136523 * (10 ** (-8))) * $Ta * $Ta * $Ta * $Ta * $D_Tmrt * $Pa +
                (8.64203390 * (10 ** (-3))) * $Ua * $D_Tmrt * $Pa +
                (-6.87405181 * (10 ** (-4))) * $Ta * $Ua * $D_Tmrt * $Pa +
                (-9.13863872 * (10 ** (-6))) * $Ta * $Ta * $Ua * $D_Tmrt * $Pa +
                (5.15916806 * (10 ** (-7))) * $Ta * $Ta * $Ta * $Ua * $D_Tmrt * $Pa +
                (-3.59217476 * (10 ** (-5))) * $Ua * $Ua * $D_Tmrt * $Pa +
                (3.28696511 * (10 ** (-5))) * $Ta * $Ua * $Ua * $D_Tmrt * $Pa +
                (-7.10542454 * (10 ** (-7))) * $Ta * $Ta * $Ua * $Ua * $D_Tmrt * $Pa +
                (-1.24382300 * (10 ** (-5))) * $Ua * $Ua * $Ua * $D_Tmrt * $Pa +
                (-7.38584400 * (10 ** (-9))) * $Ta * $Ua * $Ua * $Ua * $D_Tmrt * $Pa +
                (2.20609296 * (10 ** (-7))) * $Ua * $Ua * $Ua * $Ua * $D_Tmrt * $Pa +
                (-7.32469180 * (10 ** (-4))) * $D_Tmrt * $D_Tmrt * $Pa +
                (-1.87381964 * (10 ** (-5))) * $Ta * $D_Tmrt * $D_Tmrt * $Pa +
                (4.80925239 * (10 ** (-6))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $Pa +
                (-8.75492040 * (10 ** (-8))) * $Ta * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $Pa +
                (2.77862930 * (10 ** (-5))) * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (-5.06004592 * (10 ** (-6))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (1.14325367 * (10 ** (-7))) * $Ta * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (2.53016723 * (10 ** (-6))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (-1.72857035 * (10 ** (-8))) * $Ta * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (-3.95079398 * (10 ** (-8))) * $Ua * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $Pa +
                (-3.59413173 * (10 ** (-7))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (7.04388046 * (10 ** (-7))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (-1.89309167 * (10 ** (-8))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (-4.79768731 * (10 ** (-7))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (7.96079978 * (10 ** (-9))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (1.62897058 * (10 ** (-9))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (3.94367674 * (10 ** (-8))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (-1.18566247 * (10 ** (-9))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (3.34678041 * (10 ** (-10))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (-1.15606447 * (10 ** (-10))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa +
                (-2.80626406) * $Pa * $Pa +
                (0.548712484) * $Ta * $Pa * $Pa +
                (-0.00399428410) * $Ta * $Ta * $Pa * $Pa +
                (-9.54009191 * (10 ** (-4))) * $Ta * $Ta * $Ta * $Pa * $Pa +
                (1.93090978 * (10 ** (-5))) * $Ta * $Ta * $Ta * $Ta * $Pa * $Pa +
                (-0.308806365) * $Ua * $Pa * $Pa +
                (0.0116952364) * $Ta * $Ua * $Pa * $Pa +
                (4.95271903 * (10 ** (-4))) * $Ta * $Ta * $Ua * $Pa * $Pa +
                (-1.90710882 * (10 ** (-5))) * $Ta * $Ta * $Ta * $Ua * $Pa * $Pa +
                (0.00210787756) * $Ua * $Ua * $Pa * $Pa +
                (-6.98445738 * (10 ** (-4))) * $Ta * $Ua * $Ua * $Pa * $Pa +
                (2.30109073 * (10 ** (-5))) * $Ta * $Ta * $Ua * $Ua * $Pa * $Pa +
                (4.17856590 * (10 ** (-4))) * $Ua * $Ua * $Ua * $Pa * $Pa +
                (-1.27043871 * (10 ** (-5))) * $Ta * $Ua * $Ua * $Ua * $Pa * $Pa +
                (-3.04620472 * (10 ** (-6))) * $Ua * $Ua * $Ua * $Ua * $Pa * $Pa +
                (0.0514507424) * $D_Tmrt * $Pa * $Pa +
                (-0.00432510997) * $Ta * $D_Tmrt * $Pa * $Pa +
                (8.99281156 * (10 ** (-5))) * $Ta * $Ta * $D_Tmrt * $Pa * $Pa +
                (-7.14663943 * (10 ** (-7))) * $Ta * $Ta * $Ta * $D_Tmrt * $Pa * $Pa +
                (-2.66016305 * (10 ** (-4))) * $Ua * $D_Tmrt * $Pa * $Pa +
                (2.63789586 * (10 ** (-4))) * $Ta * $Ua * $D_Tmrt * $Pa * $Pa +
                (-7.01199003 * (10 ** (-6))) * $Ta * $Ta * $Ua * $D_Tmrt * $Pa * $Pa +
                (-1.06823306 * (10 ** (-4))) * $Ua * $Ua * $D_Tmrt * $Pa * $Pa +
                (3.61341136 * (10 ** (-6))) * $Ta * $Ua * $Ua * $D_Tmrt * $Pa * $Pa +
                (2.29748967 * (10 ** (-7))) * $Ua * $Ua * $Ua * $D_Tmrt * $Pa * $Pa +
                (3.04788893 * (10 ** (-4))) * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (-6.42070836 * (10 ** (-5))) * $Ta * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (1.16257971 * (10 ** (-6))) * $Ta * $Ta * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (7.68023384 * (10 ** (-6))) * $Ua * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (-5.47446896 * (10 ** (-7))) * $Ta * $Ua * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (-3.59937910 * (10 ** (-8))) * $Ua * $Ua * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (-4.36497725 * (10 ** (-6))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (1.68737969 * (10 ** (-7))) * $Ta * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (2.67489271 * (10 ** (-8))) * $Ua * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (3.23926897 * (10 ** (-9))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa * $Pa +
                (-0.0353874123) * $Pa * $Pa * $Pa +
                (-0.221201190) * $Ta * $Pa * $Pa * $Pa +
                (0.0155126038) * $Ta * $Ta * $Pa * $Pa * $Pa +
                (-2.63917279 * (10 ** (-4))) * $Ta * $Ta * $Ta * $Pa * $Pa * $Pa +
                (0.0453433455) * $Ua * $Pa * $Pa * $Pa +
                (-0.00432943862) * $Ta * $Ua * $Pa * $Pa * $Pa +
                (1.45389826 * (10 ** (-4))) * $Ta * $Ta * $Ua * $Pa * $Pa * $Pa +
                (2.17508610 * (10 ** (-4))) * $Ua * $Ua * $Pa * $Pa * $Pa +
                (-6.66724702 * (10 ** (-5))) * $Ta * $Ua * $Ua * $Pa * $Pa * $Pa +
                (3.33217140 * (10 ** (-5))) * $Ua * $Ua * $Ua * $Pa * $Pa * $Pa +
                (-0.00226921615) * $D_Tmrt * $Pa * $Pa * $Pa +
                (3.80261982 * (10 ** (-4))) * $Ta * $D_Tmrt * $Pa * $Pa * $Pa +
                (-5.45314314 * (10 ** (-9))) * $Ta * $Ta * $D_Tmrt * $Pa * $Pa * $Pa +
                (-7.96355448 * (10 ** (-4))) * $Ua * $D_Tmrt * $Pa * $Pa * $Pa +
                (2.53458034 * (10 ** (-5))) * $Ta * $Ua * $D_Tmrt * $Pa * $Pa * $Pa +
                (-6.31223658 * (10 ** (-6))) * $Ua * $Ua * $D_Tmrt * $Pa * $Pa * $Pa +
                (3.02122035 * (10 ** (-4))) * $D_Tmrt * $D_Tmrt * $Pa * $Pa * $Pa +
                (-4.77403547 * (10 ** (-6))) * $Ta * $D_Tmrt * $D_Tmrt * $Pa * $Pa * $Pa +
                (1.73825715 * (10 ** (-6))) * $Ua * $D_Tmrt * $D_Tmrt * $Pa * $Pa * $Pa +
                (-4.09087898 * (10 ** (-7))) * $D_Tmrt * $D_Tmrt * $D_Tmrt * $Pa * $Pa * $Pa +
                (0.614155345) * $Pa * $Pa * $Pa * $Pa +
                (-0.0616755931) * $Ta * $Pa * $Pa * $Pa * $Pa +
                (0.00133374846) * $Ta * $Ta * $Pa * $Pa * $Pa * $Pa +
                (0.00355375387) * $Ua * $Pa * $Pa * $Pa * $Pa +
                (-5.13027851 * (10 ** (-4))) * $Ta * $Ua * $Pa * $Pa * $Pa * $Pa +
                (1.02449757 * (10 ** (-4))) * $Ua * $Ua * $Pa * $Pa * $Pa * $Pa +
                (-0.00148526421) * $D_Tmrt * $Pa * $Pa * $Pa * $Pa +
                (-4.11469183 * (10 ** (-5))) * $Ta * $D_Tmrt * $Pa * $Pa * $Pa * $Pa +
                (-6.80434415 * (10 ** (-6))) * $Ua * $D_Tmrt * $Pa * $Pa * $Pa * $Pa +
                (-9.77675906 * (10 ** (-6))) * $D_Tmrt * $D_Tmrt * $Pa * $Pa * $Pa * $Pa +
                (0.0882773108) * $Pa * $Pa * $Pa * $Pa * $Pa +
                (-0.00301859306) * $Ta * $Pa * $Pa * $Pa * $Pa * $Pa +
                (0.00104452989) * $Ua * $Pa * $Pa * $Pa * $Pa * $Pa +
                (2.47090539 * (10 ** (-4))) * $D_Tmrt * $Pa * $Pa * $Pa * $Pa * $Pa +
                (0.00148348065) * $Pa * $Pa * $Pa * $Pa * $Pa * $Pa);
        }
        else {
            $UTCI_approx = null;
        }

        return $UTCI_approx;
    }

    public function system($Ta, $mrt, $RH, $Ua, float $M = 80., float $Icl = 0.9, string $bodyPosition = "standing", string $sex = "male", float $mbody = 75., float $age = 35., float $ht = 1.8) {
        /* Body system parameters for PET calculation */
        # @authors: Edouard Walther and Quentin Goestschel
        #     PET calculation after the LadyBug plugin (retrieved on Djordje Spasic's github :
        #    https://github.com/stgeorges/ladybug/commit/b0c2ea970252b62d22bf0e35d739db7f385a3f26)
        #
        #    2017.11.10 by Edouard Walther and Quentin Goestschel:
        #        - fixed the error on the reference environment (see paper)
        #

        # To avoid a lot of edits, translate variables from new to old names
        $ta = $Ta;
        $tmrt = $mrt;
        $v_air = $Ua;

        # Use relative humidity in fraction form to compute vpa
        if ($RH > 1) {
            $hfrac = $RH / 100.;
        } else {
            $hfrac = $RH;
        }

        $vpa = $hfrac * $this->es($Ta);

        # Other parameters
        $po = 1013.25; # atmospheric pressure [hPa]
        $p = 1013.25; # real pressure [hPa]
        $rob = 1.06; # Blood density kg/L
        $cb = 3640.0; # Blood specific heat [j/kg/k]
        $emsk = 0.99; # Skin emissivity [-]
        $emcl = 0.95; # Clothes emissivity [-]
        $Lv = $this->Lvap($ta);
        $sigm = 5.67 * pow(10.0, -8.0); # Stefan-Boltzmann constant [W/(m2*K^(-4))]
        $cair = 1010.0; # Air specific heat [J./kg/K-]
        $rdsk = 0.79 * pow(10.0, 7.0); # Skin diffusivity
        $rdcl = 0.0; # Clothes diffusivity
        $Adu = 0.203 * pow($mbody, 0.425) * pow($ht, 0.725); # Dubois body area
        $feff = 0.725;

        # Initialisation of the temperature set values
        $tc_set = 36.6;
        $tsk_set = 34;
        $tbody_set = 0.1 * $tsk_set + 0.9 * $tc_set;

        # Area parameters of the body: #
        if ($Icl < 0.03) {
            $Icl = 0.02;
        }
        $icl = $Icl; # [clo] Clothing level
        $eta = 0.0; # Body efficiency

        # Calculation of the Burton coefficient, k = 0.31 for Hoeppe:
        $fcl = 1 + (0.31 * $icl); # Increase of the exchange area depending on the clothing level:
        if ($bodyPosition == "sitting") {
            $feff = 0.696;
        } else if ($bodyPosition == "standing") {
            $feff = 0.725;
        } else if ($bodyPosition == "crouching") {
            $feff = 0.67;
        }

        $facl = (173.51 * $icl - 2.36 - 100.76 * $icl * $icl + 19.28 * pow($icl, 3.0)) / 100.0;

        # Basic metabolism for men and women in [W/m2] #
        # Attribution of internal energy depending on the sex of the subject

        if ($sex == "male") {
            $met_base = 3.45 * pow($mbody, 0.75) * (1.0 + 0.004 * (30.0 - $age) + 0.01 * ($ht * 100.0 / pow($mbody, 1.0 / 3.0) - 43.4));
        } else {
            $met_base = 3.19 * pow($mbody, 0.75) * (1.0 + 0.004 * (30.0 - $age) + 0.018 * ($ht * 100.0 / pow($mbody, 1.0 / 3.0) - 42.1));
        }

        # Source term : metabolic activity
        $he = $M + $met_base;
        $h = $he * (1.0 - $eta);

        # Respiratory energy losses #
        # Expired air temperature calculation:
        $texp = 0.47 * $Ta + 21.0;

        # Pulmonary flow rate
        $rtv = $he * 1.44 * pow(10.0, -6.0);

        # Sensible heat energy loss:
        $Cres = $cair * ($ta - $texp) * $rtv;

        # Latent heat energy loss:
        $vpexp = 6.11 * pow(10.0, 7.45 * $texp / (235.0 + $texp)); # Partial pressure of the breathing air
        $Eres = 0.623 * $Lv / $p * ($vpa - $vpexp) * $rtv;

        # total breathing heat loss
        $qresp = ($Cres + $Eres);

        $c = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $tcore = [0, 0, 0, 0, 0, 0, 0]; # Core temperature list
        $hc = 2.67 + 6.5 * pow($v_air, 0.67); # Convection coefficient
        $hc = $hc * pow($p / $po, 0.55); # Correction with pressure

        # Clothed fraction of the body approximation #
        $rcl = $icl / 6.45; # conversion in m2.K/W
        $y = 0;
        if ($facl > 1.0) {
            $facl = 1.0;
            $rcl = $icl / 6.45; # conversion clo --> m2.K/W
        }
        # y : equivalent clothed height of the cylinder
        # High clothing level : all the height of the cylinder is covered
        if ($icl >= 2.0) {
            $y = 1.0;
        }
        if ($icl > 0.6 and $icl < 2.0) {
            $y = ($ht - 0.2) / $ht;
        }
        if ($icl <= 0.6 and $icl > 0.3) {
            $y = 0.5;
        }
        if ($icl <= 0.3 and $icl > 0.0) {
            $y = 0.1;
        }
        # calculation of the closing radius depending on the clothing level (6.28 = 2 * pi !)
        $r2 = $Adu * ($fcl - 1.0 + $facl) / (6.28 * $ht * $y); # External radius
        $r1 = $facl * $Adu / (6.28 * $ht * $y); # Internal radius
        $di = $r2 - $r1;
        # clothed surface
        $Acl = $Adu * $facl + $Adu * ($fcl - 1.0);
        # skin temperatures
        for ($j = 1; $j < 7; $j++) {
            $tsk = $tsk_set;
            $count1 = 0;
            $tcl = ($ta + $tmrt + $tsk) / 3.0; # Average value between the temperatures to estimate Tclothes
            $enbal2 = 0.0;
            $isCalculatingSkinTemperatures = true;
            while ($isCalculatingSkinTemperatures) {
                for ($count2 = 1; $count2 < 100; $count2++) {
                    # Estimation of the radiation losses
                    $rclo2 = $emcl * $sigm * (pow($tcl + 273.2, 4.0) - pow($tmrt + 273.2, 4.0)) * $feff;
                    # Calculation of the thermal resistance of the body:
                    $htcl = (6.28 * $ht * $y * $di) / ($rcl * log($r2 / $r1) * $Acl);
                    $tsk = ($hc * ($tcl - $ta) + $rclo2) / $htcl + $tcl; # Skin temperature calculation

                    # Radiation losses #
                    $Aeffr = $Adu * $feff; # Effective radiative area depending on the position of the subject
                    # For bare skin area:
                    $rbare = $Aeffr * (1.0 - $facl) * $emsk * $sigm * (pow($tmrt + 273.2, 4.0) - pow($tsk + 273.2, 4.0));
                    # For dressed area:
                    $rclo = $feff * $Acl * $emcl * $sigm * (pow($tmrt + 273.2, 4.0) - pow($tcl + 273.2, 4.0));
                    $rsum = $rbare + $rclo; #[W]

                    # Convection losses #
                    $cbare = $hc * ($ta - $tsk) * $Adu * (1.0 - $facl);
                    $cclo = $hc * ($ta - $tcl) * $Acl;
                    $csum = $cbare + $cclo; #[W]

                    # Calculation of the Terms of the second order polynomial :
                    $K_blood = $Adu * $rob * $cb;
                    $c[0] = $h + $qresp;
                    $c[2] = $tsk_set / 2 - 0.5 * $tsk;
                    $c[3] = 5.28 * $Adu * $c[2];
                    $c[4] = 13.0 / 625.0 * $K_blood;
                    $c[5] = 0.76275 * $K_blood;
                    $c[6] = $c[3] - $c[5] - $tsk * $c[4];
                    $c[7] = -$c[0] * $c[2] - $tsk * $c[3] + $tsk * $c[5];
                    $c[9] = 5.28 * $Adu - 0.76275 * $K_blood - 13.0 / 625.0 * $K_blood * $tsk;
                    # discriminant #1 (b^2 - 4*a*c)
                    $c[10] = pow((5.28 * $Adu - 0.76275 * $K_blood - 13.0 / 625.0 * $K_blood * $tsk), 2) - 4.0 * $c[4] * ($c[5] * $tsk - $c[0] - 5.28 * $Adu * $tsk);
                    # discriminant #2 (b^2 - 4*a*c)
                    $c[8] = $c[6] * $c[6] - 4.0 * $c[4] * $c[7];
                    if ($tsk == $tsk_set) {
                        $tsk = $tsk_set + 0.01;
                    }
                    # Calculation of Tcore[]:
                    # case 6 : Set blood flow only
                    $tcore[6] = ($h + $qresp) / (5.28 * $Adu + $K_blood * 6.3 / 3600.0) + $tsk;
                    # cas 2 : Set blood flow + regulation
                    $tcore[2] = ($h + $qresp) / (5.28 * $Adu + $K_blood * 6.3 / 3600.0 / (1.0 + 0.5 * ($tsk_set - $tsk))) + $tsk;
                    # case 3 : Maximum blood flow only
                    $tcore[3] = $c[0] / (5.28 * $Adu + $K_blood * 1.0 / 40.0) + $tsk; # max flow = 90 [L/m2/h]/3600 <=> 1/40
                    # Roots calculation #1
                    if ($c[10] >= 0.0) { # Numerical safety to avoid negative roots
                        $tcore[5] = (-$c[9] - pow($c[10], 0.5)) / (2.0 * $c[4]);
                        $tcore[0] = (-$c[9] + pow($c[10], 0.5)) / (2.0 * $c[4]);
                    }
                    # Roots calculation #2
                    if ($c[8] >= 0.0) {
                        $tcore[1] = (-$c[6] + pow(abs($c[8]), 0.5)) / (2.0 * $c[4]);
                        $tcore[4] = (-$c[6] - pow(abs($c[8]), 0.5)) / (2.0 * $c[4]);
                    }

                    # Calculation of sweat losses #
                    $tbody = 0.1 * $tsk + 0.9 * $tcore[$j - 1];
                    # Sweating flow calculation
                    $swm = 304.94 * ($tbody - $tbody_set) * $Adu / 3600000.0;
                    # Saturation vapor pressure at temperature Tsk and for 100% HR
                    $vpts = 6.11 * pow(10.0, 7.45 * $tsk / (235.0 + $tsk));
                    if ($tbody <= $tbody_set) {
                        $swm = 0.0;
                    }
                    if ($sex == 2) {
                        $swm = 0.7 * $swm;
                    }
                    $esweat = -$swm * $Lv;
                    $hm = 0.633 * $hc / ($p * $cair); # Evaporation coefficient [W/(m^2*Pa)]
                    $fec = 1.0 / (1.0 + 0.92 * $hc * $rcl);
                    $emax = $hm * ($vpa - $vpts) * $Adu * $Lv * $fec; # Max latent flux
                    $wetsk = $esweat / $emax; # skin wetness
                    # esw: Latent flux depending on w [W.m-2]
                    if ($wetsk > 1.0) {
                        $wetsk = 1.0;
                    }
                    $eswdif = $esweat - $emax; # difference between sweating and max capacity
                    if ($eswdif <= 0.0) {
                        $esw = $emax;
                    }
                    if ($eswdif > 0.0) {
                        $esw = $esweat;
                    }
                    if ($esw > 0.0) {
                        $esw = 0.0;
                    }
                    $ed = $Lv / ($rdsk + $rdcl) * $Adu * (1.0 - $wetsk) * ($vpa - $vpts); # diffusion heat flux

                    $vb1 = $tsk_set - $tsk; # difference for the volume blood flow calculation
                    $vb2 = $tcore[$j - 1] - $tc_set; # idem
                    if ($vb2 < 0.0) {
                        $vb2 = 0.0;
                    }
                    if ($vb1 < 0.0) {
                        $vb1 = 0.0;
                    }
                    # Calculation of the blood flow depending on the difference with the set value
                    $vb = (6.3 + 75 * $vb2) / (1.0 + 0.5 * $vb1);
                    # energy balance MEMI modele
                    $enbal = $h + $ed + $qresp + $esw + $csum + $rsum;
                    # clothing temperature
                    if ($count1 == 0) {
                        $xx = 1.0;
                    }
                    if ($count1 == 1) {
                        $xx = 0.1;
                    }
                    if ($count1 == 2) {
                        $xx = 0.01;
                    }
                    if ($count1 == 3) {
                        $xx = 0.001;
                    }
                    if ($enbal > 0.0) {
                        $tcl = $tcl + $xx;
                    }
                    if ($enbal < 0.0) {
                        $tcl = $tcl - $xx;
                    }
                    if (($enbal > 0.0 or $enbal2 <= 0.0) and ($enbal < 0.0 or $enbal2 >= 0.0)) {
                        $enbal2 = $enbal;
                        $count2 += 1;
                    } else {
                        break;
                    }
                }
                if ($count1 == 0.0 or $count1 == 1.0 or $count1 == 2.0) {
                    $count1 = $count1 + 1;
                    $enbal2 = 0.0;
                }
                else {
                    break;
                }
            # end "While True" (using 'break' statements)
            for ($k = 0; $k < 20; $k++) {
                $g100 = 0;
                if ($count1 == 3.0 and ($j != 2 and $j != 5)) {
                    if ($j != 6 and $j != 1) {
                        if ($j != 3) {
                            if ($j != 7) {
                                if ($j == 4) {
                                    $g100 = true;
                                    break;
                                }
                            }
                            else {
                                if ($tcore[$j - 1] >= $tc_set or $tsk <= $tsk_set) {
                                    $g100 = false;
                                    break;
                                }
                                $g100 = true;
                                break;
                            }
                        }
                        else {
                            if ($tcore[$j - 1] >= $tc_set or $tsk > $tsk_set) {
                                $g100 = false;
                                break;
                            }
                            $g100 = true;
                            break;
                        }
                    }
                    else {
                        if ($c[10] < 0.0 or ($tcore[$j - 1] < $tc_set or $tsk <= 33.85)) {
                            $g100 = false;
                            break;
                        }
                        $g100 = true;
                        break;
                    }
                }
                if ($c[8] < 0.0 or ($tcore[$j - 1] < $tc_set or $tsk > $tsk_set + 0.05)) {
                    $g100 = false;
                    break;
                }
            }
            if ($g100 == false) {
                continue;
            }
            else {
                if (($j == 4 or $vb < 91.0) and ($j != 4 or $vb >= 89.0)) {
                    # Maximum blood flow
                    if ($vb > 90.0) {
                        $vb = 90.0;
                    }
                    # water loss in g/m2/h
                    $ws = $swm * 3600.0 * 1000.0;
                    if ($ws > 2000.0) {
                        $ws = 2000.0;
                    }
                    # wd and wr are not used at present. Original code lines are kept as comment
                    # to avoid errors, but allow possible future use by decommenting
                    # $wd = $ed / $Lv * 3600.0 * (-1000.0);
                    # $wr = $Eres / $Lv * 3600.0 * (-1000.0);
                    return [$tcore[$j - 1], $tsk, $tcl, $esw];
                }
            }
            # water loss
            $ws = $swm * 3600.0 * 1000.0; # sweating
            # wd and wr are not used at present. Original code lines are kept as comment
            # to avoid errors,  but allow possible future use by decommenting
            # $wd = $ed / $Lv * 3600.0 * (-1000.0); # diffusion = perspiration
            # $wr = $Eres / $Lv * 3600.0 * (-1000.0); # respiration latent

            if ($j - 3 < 0) {
                $index = 3;
            } else {
                $index = $j - 3;
            }

            return [$tcore[$index], $tsk, $tcl, $esw];
        }
    }
}

    public function pet($tc, $tsk, $tcl, $ta_init, $esw_real, float $M = 80., $Icl = 0.9, string $bodyPosition = "standing", string $sex = "male", float $mbody = 75., float $age = 35., float $ht = 1.8) {

        $po = 1013.25; # atmospheric pressure [hPa]
        $p = 1013.25; # real pressure [hPa]
        $emsk = 0.99; # Skin emissivity [-]
        $emcl = 0.95; # Clothes emissivity [-]
        $Lv = $this->Lvap($ta_init);
        $sigm = 5.67 * pow(10.0, -8.0); # Stefan-Boltzmann constant [W/(m2*K^(-4))]
        $cair = 1010.0; # Air specific heat [J./kg/K-]
        $rdsk = 0.79 * pow(10.0, 7.0); # Skin diffusivity
        $rdcl = 0.0; # Clothes diffusivity
        $Adu = 0.203 * pow($mbody, 0.425) * pow($ht, 0.725); # Dubois body area
        $feff = 0.725;
        $tc_set = 36.6;
        $tsk_set = 34;
        $tbody_set = 0.1 * $tsk_set + 0.9 * $tc_set;

        # Input variables of the PET reference situation:

        $icl_ref = 0.9; # clo
        $M_activity_ref = 80; # W
        $v_air_ref = 0.1; # m/s
        $vpa_ref = 12; # hPa
        $icl = $icl_ref;

        $tx = $ta_init;
        $tbody = 0.1 * $tsk + 0.9 * $tc;
        $enbal2 = 0.0;
        $count1 = 0;

        # base metabolism
        if ($sex == "male") {
            $met_base = 3.45 * pow($mbody, 0.75) * (1.0 + 0.004 * (30.0 - $age) + 0.01 * ($ht * 100.0 / pow($mbody, 1.0 / 3.0) - 43.4));
        } else {
            $met_base = 3.19 * pow($mbody, 0.75) * (1.0 + 0.004 * (30.0 - $age) + 0.018 * ($ht * 100.0 / pow($mbody, 1.0 / 3.0) - 42.1));
        }
        # breathing flow rate
        $rtv_ref = ($M_activity_ref + $met_base) * 1.44 * pow(10.0, -6.0);

        $swm = 304.94 * ($tbody - $tbody_set) * $Adu / 3600000.0; # sweating flow rate
        $vpts = 6.11 * pow(10.0, 7.45 * $tsk / (235.0 + $tsk)); # saturated vapour pressure at skin surface
        if ($tbody <= $tbody_set) {
            $swm = 0.0;
        }
        if ($sex == "female") {
            $swm = $swm * 0.7;
        }
        $esweat = -$swm * $Lv;
        $esweat = $esw_real;
        # standard environment
        $hc = 2.67 + 6.5 * pow($v_air_ref, 0.67);
        $hc = $hc * pow($p / $po, 0.55);
        # radiation saldo
        $Aeffr = $Adu * $feff;
        $facl = (173.51 * $icl - 2.36 - 100.76 * $icl * $icl + 19.28 * pow($icl, 3.0)) / 100.0;
        if ($facl > 1.0) {
            $facl = 1.0;
        }
        # Increase of the exchange area depending on the clothing level
        $fcl = 1 + (0.31 * $icl);
        $Acl = $Adu * $facl + $Adu * ($fcl - 1.0);
        $hm = 0.633 * $hc / ($p * $cair); # Evaporation coefficient [W/(m^2*Pa)]
        $fec = 1.0 / (1.0 + 0.92 * $hc * 0.155 * $icl_ref); # vapour transfer efficiency for reference clothing
        $emax = $hm * ($vpa_ref - $vpts) * $Adu * $Lv * $fec; # max latent flux for the reference vapour pressure 12 hPa
        $wetsk = $esweat / $emax;
        # skin wetness
        if ($wetsk > 1.0) {
            $wetsk = 1.0;
        }
        $eswdif = $esweat - $emax;
        # diffusion
        $ediff = $Lv / ($rdsk + $rdcl) * $Adu * (1.0 - $wetsk) * ($vpa_ref - $vpts);
        # esw: sweating [W.m-2] from the actual environment : in depends only on the difference with the core set temperature
        if ($eswdif <= 0.0) {
            $esw = $emax;
        }
        if ($eswdif > 0.0) {
            $esw = $esweat;
        }
        if ($esw > 0.0) {
            $esw = 0.0;
        }

        while ($count1 != 4) {
            $rbare = $Aeffr * (1.0 - $facl) * $emsk * $sigm * (pow($tx + 273.2, 4.0) - pow($tsk + 273.2, 4.0));
            $rclo = $feff * $Acl * $emcl * $sigm * (pow($tx + 273.2, 4.0) - pow($tcl + 273.2, 4.0));
            $rsum = $rbare + $rclo; # Recalculation of the radiative losses
            # convection
            $cbare = $hc * ($tx - $tsk) * $Adu * (1.0 - $facl);
            $cclo = $hc * ($tx - $tcl) * $Acl;
            $csum = $cbare + $cclo; # Recalculation of the convective losses
            # breathing
            $texp = 0.47 * $tx + 21.0;
            $Cres = $cair * ($tx - $texp) * $rtv_ref;
            $vpexp = 6.11 * pow(10.0, 7.45 * $texp / (235.0 + $texp));
            $Eres = 0.623 * $Lv / $p * ($vpa_ref - $vpexp) * $rtv_ref;
            $qresp = ($Cres + $Eres);
            # ----------------------------------------
            # energy balance
            $enbal = ($M_activity_ref + $met_base) + $ediff + $qresp + $esw + $csum + $rsum;
            if ($count1 == 0) {
                $xx = 1.0;
            }
            if ($count1 == 1) {
                $xx = 0.1;
            }
            if ($count1 == 2) {
                $xx = 0.01;
            }
            if ($count1 == 3) {
                $xx = 0.001;
            }
            if ($enbal > 0.0) {
                $tx = $tx - $xx;
            }
            if ($enbal < 0.0) {
                $tx += $xx;
            }
            if (($enbal > 0.0 or $enbal2 <= 0.0) and ($enbal < 0.0 or $enbal2 >= 0.0)) {
                $enbal2 = $enbal;
            } else {
                $count1 = $count1 + 1;
            }
        }
        return $tx;
    }

    /**
     * @param string $createdDateTime usually created_at
     * @param float $airTemperature usually th_temp
     * @param float $solarRadiation usually sol_rad
     * @param float $humidity usually th_hum
     * @param float $windSpeed usually wind_avgwind
     * @param float $latitude
     * @param float $longitude
     * @return float|null Physiologically Equivalent Temperature in Celsius
     */
    public function computePETFromMeasurement(?string $createdDateTime,
                                              ?float $airTemperature,
                                              ?float $solarRadiation,
                                              ?float $humidity,
                                              ?float $windSpeed,
                                              ?float $latitude = 52.,
                                              ?float $longitude = 5.1) {

        /*
            Check if necessary parameters aren't null
            otherwise return null.
            This may change in the future.
        */
        if ($createdDateTime === null or
            $airTemperature === null or
            $solarRadiation === null or
            $humidity === null or
            $windSpeed === null) {
            return null;
        }

        // Construct DateTime from string
        $createdDateTime = new DateTime($createdDateTime);
        $createdDateTime = $createdDateTime->setTimezone(new DateTimeZone('UTC'));
        // date 'z' returns day of the year starting from 0, so add 1
        $dayOfTheYear = date('z', $createdDateTime->getTimestamp()) + 1;
        $decimalTime =   floatval($createdDateTime->format('H')) +
                        (floatval($createdDateTime->format('i'))/60) +
                        (floatval($createdDateTime->format('s'))/3600);

        $cza = $this->sin_solar_elev($latitude,
                                        $longitude,
                                        $dayOfTheYear,
                                        $decimalTime);
        // Correct for incorrect values of solar radiation
        if ($solarRadiation < 0) {
            $solarRadiation = 0;
        }
        else if ($solarRadiation > 75. + 1.2 * $this->solar_clear (  $longitude,
                                                                    $latitude,
                                                                    $dayOfTheYear,
                                                                    $decimalTime)
        || ((-0.02 < $cza) && ($cza < 0.02))) {
            $solarRadiation = $this->solar_clear(   $longitude,
                                                    $latitude,
                                                    $dayOfTheYear,
                                                    $decimalTime);

        }

        // Currently no urban correction applied
//         $urbanFactor = log(10/1.)/log(3.5/1.);
        $urbanFactor = 1.;

        // Get fraction of diffuse and direct solar radiation
        $fractionOfDiffuseSolarRadiation = $this->fr_diffuse(   $solarRadiation,
                                                                $latitude,
                                                                $longitude,
                                                                $dayOfTheYear,
                                                                $decimalTime);
        $fractionOfDirectSolarRadiation = 1. - $fractionOfDiffuseSolarRadiation;

        // Get cosine of zenith angle
        $cosineOfZenithAngle = $this->sin_solar_elev(   $latitude,
                                                        $longitude,
                                                        $dayOfTheYear,
                                                        $decimalTime);

        // Get globe temperature
        $globeTemperature = $this->calc_Tglobe( $airTemperature,
                                                $humidity,
                                            $urbanFactor * $windSpeed,
                                                $solarRadiation,
                                                $fractionOfDirectSolarRadiation,
                                                $cosineOfZenithAngle);
        // If calc_Tglobe returns NAN, then return null
        if (is_nan($globeTemperature)) {
            return null;
        }

        // Get median radiant temperature
        $medianRadiantTemperature = $this->Tmrt($globeTemperature,
                                                $airTemperature,
                                                $windSpeed);

        // Get system output
        $systemOutput = $this->system(  $airTemperature,
                                        $medianRadiantTemperature,
                                        $humidity,
                                        $windSpeed);
        $coreTemperature = $systemOutput[0];
        $temperatureOfSkin = $systemOutput[1];
        $temperatureOfClothes = $systemOutput[2];
        $evaporationOfSweat = $systemOutput[3];

        // Calculate and return PET value
        return $this->pet(  $coreTemperature,
                            $temperatureOfSkin,
                            $temperatureOfClothes,
                            $airTemperature,
                            $evaporationOfSweat);
    }

    #*************************************************************************************
    # Block 4:
    # Various functions

    public function viscosity($Ta) {
        /*
           Viscosity of air, using Ta in Kelvin
        */

        $mair = 28.97;
        $epskappa = 97.0;
        $sig = 3.617;
        $sig2 = $sig ** 2;
        $tr = $Ta / $epskappa;
        $omega = (-0.034) * ($tr - 2.9) / 0.4 + 1.048;
        $viscosity = 2.6693E-6 * sqrt($mair * $Ta) / ($sig2 * $omega);

        return ($viscosity);
    }

    public function thermal_cond($Ta) {
        /*
          Thermal conductivity
        */

        $mair = 28.97;
        $rgas = 8314.34;
        $cp = 1003.5;
        $rair = $rgas / $mair;
        $thermal_cond = ($cp + 1.25 * $rair) * $this->viscosity($Ta);

        return ($thermal_cond);
    }

    public function h_sphere_in_air($diameter, $Ta, $Pa, $Ua) {
        /*
           Heat conduction of a sphere in air.
           Note: Provide pressure Pa in hPa and temperature Ta in Kelvin
        */

        $mair = 28.97;
        $rgas = 8314.34;
        $rair = $rgas / $mair;
        $cp = 1003.5;
        $pr = $cp / ($cp + 1.25 * $rair);
        $min_Ua = 0.13; #threshold of wind speed anemometers
        $density = $Pa * 100. / ($rair * $Ta); # kg/m3
        $re = max($Ua, $min_Ua) * $density * $diameter / $this->viscosity($Ta);
        $nu = 2.0 + 0.6 * $re ** 0.5 * $pr ** 0.3333;
        $h_sphere_in_air = $nu * $this->thermal_cond($Ta) / $diameter; # w/(m2 K)

        return ($h_sphere_in_air);
    }
}

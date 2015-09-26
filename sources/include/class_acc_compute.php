<?php
/*
 *   This file is part of NOALYSS.
 *
 *   NOALYSS is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   NOALYSS is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with NOALYSS; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Copyright Author Dany De Bontridder danydb@aevalys.eu

/*!\file
 * \brief to compute the different amount of an invoice of an expense,
 */

/**
 * @brief  this class aims to compute different amount
 *
 * This class compute without decimal error the following amount
 * - vat
 * - amount without vat
 * - no deductible vat
 * - personal part
 * - no deductible amount
 * Nothing won't be saved to the database, this class will just
 * compute and complete the object
 * if you need to compute the vat and in another place all the
 * details you'll have to use the clone function
 private static $variable=array( 'amount'=>'amount',
		  'amount_vat'=>'amount_vat',
		  'amount_vat_rate'=>'amount_vat_rate',
		  'nd_vat'=>'nd_vat',
		  'nd_vat_rate'=>'nd_vat_rate',
		  'nd_ded_vat'=>'nd_ded_vat',
		  'nd_ded_vat_rate'=>'nd_ded_vat_rate',
		  'amount_nd'=>'amount_nd',
		  'amount_nd_rate'=>'amount_nd_rate',
		  'nd_vat_rate'=>'nd_vat_rate',
		  'amount_perso'=>'amount_perso',
		  'amount_perso_rate'=>'amount_perso_rate'				  );

 */


class Acc_Compute
{
    private static $variable=array( 'amount'=>'amount',
                                    'amount_vat'=>'amount_vat',
                                    'amount_vat_rate'=>'amount_vat_rate',
                                    'nd_vat'=>'nd_vat',
                                    'nd_vat_rate'=>'nd_vat_rate',
                                    'nd_ded_vat'=>'nd_ded_vat',
                                    'nd_ded_vat_rate'=>'nd_ded_vat_rate',
                                    'amount_nd'=>'amount_nd',
                                    'amount_nd_rate'=>'amount_nd_rate',
                                    'nd_vat_rate'=>'nd_vat_rate',
                                    'amount_perso'=>'amount_perso',
                                    'amount_perso_rate'=>'amount_perso_rate'
                                  );

    private  $order;			// check that the compute
    // function are  called in the
    // good order

    var $check;				// activate the check of the
    // order, valid value are
    // false or true
    function __construct ()
    {
        bcscale(4);
        foreach (self::$variable as $key=>$value)       $this->$key=0;
        $this->order=0;
        $this->check=true;
    }

    public function get_parameter($p_string)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            return $this->$idx;
        }
        else
            throw new Exception (__FILE__.":".__LINE__._('Erreur attribut inexistant'));
    }
    public function set_parameter($p_string,$p_value)
    {
        if ( array_key_exists($p_string,self::$variable) )
        {
            $idx=self::$variable[$p_string];
            $this->$idx=$p_value;
        }
        else
            throw new Exception (__FILE__.":".__LINE__._('Erreur attribut inexistant'));


    }
    public function get_info()
    {
        return var_export(self::$variable,true);
    }

    function compute_vat()
    {
        if ( $this->check && $this->order != 0 ) throw new Exception ('ORDER NOT RESPECTED');
        $this->amount_vat=bcmul($this->amount,$this->amount_vat_rate);
        $this->amount_vat=round($this->amount_vat,2);
        $this->order=1;
    }
    /*!\brief Compute the no deductible part of the amount, it reduce
     *also the vat
     */
    function compute_nd()
    {
        if ( $this->check && $this->order > 2 )  throw new Exception ('ORDER NOT RESPECTED');

        $this->amount_nd=bcmul($this->amount,$this->amount_nd_rate);
        $this->amount_nd=bcdiv($this->amount_nd,100);
        $this->amount_nd=round($this->amount_nd,2);
        // the nd part for the vat
        $nd_vat=bcmul($this->amount_vat,$this->amount_nd_rate);
        $nd_vat=bcdiv($nd_vat,100);
        $nd_vat=round($nd_vat,2);

    }
    function compute_nd_vat()
    {
        if ( $this->check && $this->order > 3 ) throw new Exception ('ORDER NOT RESPECTED');
        $this->order=4;

        if ($this->amount_vat == 0 ) $this->compute_vat();
        $this->nd_vat=bcmul($this->amount_vat,$this->nd_vat_rate);
        $this->nd_vat=bcdiv($this->nd_vat,100);
        $this->nd_vat=round($this->nd_vat,2);
    }

    function compute_ndded_vat()
    {
        if ( $this->check && $this->order > 4 ) throw new Exception ('ORDER NOT RESPECTED');
        $this->order=5;

        if ($this->amount_vat == 0 ) $this->compute_vat();
        $this->nd_ded_vat=bcmul($this->amount_vat,$this->nd_ded_vat_rate);
        $this->nd_ded_vat=bcdiv($this->nd_ded_vat,100);
        $this->nd_ded_vat=round($this->nd_ded_vat,2);
    }

    function compute_perso()
    {
        if ( $this->check && $this->order != 1 ) throw new Exception ('ORDER NOT RESPECTED');
        $this->order=2;
        if ( $this->amount == 0 ) return;
        $this->amount_perso=bcmul($this->amount,$this->amount_perso_rate);
        $this->amount_perso=bcdiv($this->amount_perso,100);
        $this->amount_perso=round($this->amount_perso,2);



    }
    function correct()
    {
        $this->amount=bcsub($this->amount,$this->amount_perso);
        // correct the others amount
        $this->amount=bcsub($this->amount,$this->amount_nd);
        $this->amount_vat=bcsub($this->amount_vat,$this->nd_ded_vat);
        $this->amount_vat=round($this->amount_vat,2);
        $this->amount_vat=bcsub($this->amount_vat,$this->nd_vat);
        $this->amount_vat=round($this->amount_vat,2);

    }

    /**!
     * \brief verify that all the amount are positive or null
     * otherwise throw a exception and the sum of amount + vat must
     * equal to the sum of all the amount of the current object
     * so you have to copy the object before computing anything and pass
     * it as parameter
     * \param compare with a object copied before computing, if null
     * there is no comparison
     */
    function verify($p_obj=null)
    {
        foreach (self::$variable as $key=>$value)
        if ( $this->$value < 0 )
            throw new Exception (_("Montant invalide"));

        if ( $p_obj != null )
        {
            $sum=0;
            foreach ( array( 'amount','amount_vat','amount_nd','nd_vat','amount_perso','nd_ded_vat') as $value)
            $sum=bcadd($sum,$this->$value);
            if ( $p_obj->amount_vat == 0 ) $p_obj->compute_vat();
            $cmp=bcadd($p_obj->amount,$p_obj->amount_vat);
            $diff=bcsub($sum,$cmp);
            if ( $diff != 0.0 )
                throw new Exception (_("ECHEC VERIFICATION  : valeur totale = $sum valeur attendue = $cmp diff = $diff"));
        }
    }
    function display()
    {
        foreach (self::$variable as $key=>$value)
        {
            echo 'key '.$key.' Description '.$value.' value is '.$this->$key.'<br>';
        }
    }
    public static function test_me ()
    {
        $a=new Acc_Compute();
        echo $a->get_info();
        echo '<hr>';

        // Compute some operation to see if the computed amount are
        // correct

        //Test VAT
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);

        echo '<h1> Test VAT </h1>';
        echo '<h2> Data </h2>';
        $a->display();

        echo '<h2> Result </h2>';
        $a->compute_vat();
        $a->display();
        $a->verify();
        // Test VAT + perso
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('amount_perso_rate',0.5);
        echo '<h1> Test VAT + Perso</h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $b=clone $a;
        $a->compute_vat();
        $a->compute_perso();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);
        // TEST VAT + ND
        // Test VAT + perso
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('nd_vat_rate',0.5);
        $b=clone $a;
        echo '<h1> Test VAT + ND VAT</h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $a->compute_vat();
        $a->compute_nd_vat();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);
        // TEST VAT + ND
        // Test VAT + perso
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('nd_vat_rate',0.5);
        $a->set_parameter('amount_perso_rate',0.5);

        $b=clone $a;
        echo '<h1> Test VAT + ND VAT + perso</h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $a->compute_vat();
        $a->compute_perso();
        $a->compute_nd_vat();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);
        // TEST VAT + ND
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('amount_nd_rate',0.5);

        $b=clone $a;
        echo '<h1> Test VAT + ND </h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $a->compute_vat();
        $a->compute_nd();

        $a->compute_perso();
        $a->compute_nd_vat();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);
        // TEST VAT + ND
        // + Perso
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('amount_nd_rate',0.5);
        $a->set_parameter('amount_perso_rate',0.2857);
        $b=clone $a;
        echo '<h1> Test VAT + ND  + Perso</h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $a->compute_vat();
        $a->compute_nd();

        $a->compute_perso();
        $a->compute_nd_vat();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);
// TEST VAT + ND
        // + Perso
        $a=new Acc_Compute();
        $a->set_parameter('amount',1.23);
        $a->set_parameter('amount_vat_rate',0.21);
        $a->set_parameter('nd_ded_vat_rate',0.5);

        $b=clone $a;
        echo '<h1> Test VAT   +  TVA ND DED</h1>';
        echo '<h2> Data </h2>';
        $a->display();
        $a->compute_vat();
        $a->compute_nd();

        $a->compute_perso();
        $a->compute_nd_vat();
        $a->compute_ndded_vat();
        $a->correct();
        echo '<h2> Result </h2>';
        $a->display();
        $a->verify($b);


    }
}

<div class="row">
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" class="custom-registration-form">               
    	<div class="row">   		
       		
            <div class="col-md-12">
            	<h1 class="createUser"><b>Opret konto</b></h1><br>
                <h2>Personlige oplysninger</h2>
            </div>
            
            <div class="col-md-12">
                <label>Kontotype : </label>
               <div> <input type="radio" name="role" class="role-select" data-id="private-person" value="Privatperson" checked="checked"/><span>Privat</span></div>
                <div><input type="radio" name="role" class="role-select" data-id="business" value="Business"/><span>Virksomhed</span></div>               
            </div>
            
            <div class="col-md-4 registration-single-row business" style="display:none">
                <label>Virksomhedsnavn *</label>
                <input type="text" name="company_name" value="<?php echo ( isset( $_POST['company_name']) ? $company_name : null ) ?>"/>
            </div>
            
            <div class="col-md-4 registration-single-row business" style="display:none">
                <label>CVR *</label>
                <input type="number" name="organisation_number" value="<?php echo ( isset( $_POST['organisation_number']) ? $organisation_number : null ) ?>" />
            </div>
            
            <div class="col-md-4">
                <label>Fornavn * </label>
                <input type="text" name="first_name" value="<?php echo ( isset( $_POST['first_name']) ? $first_name : null ) ?>" required/>
            </div>
            
            <div class="col-md-4">
                <label>Efternavn * </label>
                <input type="text" name="last_name" value="<?php echo ( isset( $_POST['last_name']) ? $last_name : null ) ?>" required/>
            </div>
            
            <div class="col-md-4 registration-single-row private-person">
                <label>Fødselsdato *</label>
                <input type="date" name="date_of_birth" value="<?php echo ( isset( $_POST['date_of_birth']) ? $date_of_birth : null ) ?>" required/>
            </div>
            
            <div class="col-md-4">
                <label>Adresse * </label>
                <input type="text" name="billing_address_1" value="<?php echo ( isset( $_POST['billing_address_1']) ? $billing_address_1 : null ) ?>" required/>
            </div>
            
            <div class="col-md-4">
                <label>Postnummer * </label>
                <input type="text" name="billing_postcode" value="<?php echo ( isset( $_POST['billing_postcode']) ? $billing_postcode : null ) ?>" required/>
            </div>
            
            <div class="col-md-4">
                <label>By * </label>
                <input type="text" name="billing_city" value="<?php echo ( isset( $_POST['billing_city']) ? $billing_city : null ) ?>" required/>
            </div>
            
            <div class="col-md-4">
                <label>Telefon * </label>
                <input type="text" name="billing_phone" value="<?php echo ( isset( $_POST['billing_phone']) ? $billing_phone : null ) ?>" required/>
            </div>
            
            <?php /*?><div class="col-md-4">
                <label>Mobil *</label>
                <input type="text" name="mobile_number" value="<?php echo ( isset( $_POST['mobile_number']) ? $mobile_number : null ) ?>" required/>
            </div><?php */?>
            
            <div class="col-md-12"><h2>Konto information</h2></div> 
            
		</div>   
                 
            <div class="row">       
                <div class="col-md-4">
                    <label for="username">Brugernavn <strong>*</strong></label>
                    <input type="text" name="username" value="<?php echo ( isset( $_POST['username'] ) ? $username : null ); ?>" required>
                </div>
                
                 <div class="col-md-4">
                    <label for="email">Email <strong>*</strong></label>
                    <input type="email" name="email" value="<?php echo ( isset( $_POST['email']) ? $email : null ); ?>" required>
                </div>
            </div>
        	
            <div class="row">  
                <div class="col-md-4">
                    <label for="password">Adgangskode <strong>*</strong> </label>
                    <input type="password" name="password" value="" required>
                    <small>(Adgangskoden skal være minimum 8 tegn og indeholde stor bogstav, lille bogstav og tal) </small>
                </div>
                
                 <div class="col-md-4">
                    <label for="password">Gentag adgangskode <strong>*</strong></label>
                    <input type="password" name="repeat_password" value="" required>
                </div>
                
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <p>
                        <label>
                            <input type="checkbox" name="mc4wp-subscribe" value="1" />
                            <span>Giv mig besked når der kommer flere biler på auktion</span></label>
                    </p>
                </div>
                <div class="col-md-12 handelsbetingelserAccept">
                    <input type="checkbox" name="acceptTerms" required>
                    <span>Jeg har læst og accepterer Webbils <a href="#">handelsbetingelser</a>.</span>
                </div>
                <br><br>
                <div class="col-md-12">
                   <div class="recaptcha-container">
                        <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option('registration_recptcha_site_key') ); ?>"></div>
                    </div>
                </div>
                
                 
                <div class="col-md-12">
                    <input type="submit" name="submit" value="Opret konto"/>
                </div>
           	</div>
	</form>   
</div>

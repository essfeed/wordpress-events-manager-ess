<?php
/**
  * Model ESS_Notices
  * Manage global var to dispash a 4 level messaging
  *                             
  * @author  	Brice Pissard
  * @author		Markus Sykes
  * @copyright 	No Copyright.
  * @license   	GNU/GPLv2, see http://www.gnu.org/licenses/gpl-2.0.html
  * @link    	http://essfeed.org
  * @link		https://github.com/essfeed 
  */ 
final class ESS_Notices
{
    var $notices = array(
    	'errors'	=> array(), 
    	'infos'		=> array(), 
    	'alerts'	=> array(), 
    	'confirms'	=> array()
	);
    
    function __construct()
    {
    	if ( !empty( $_COOKIE[ 'ess_notices' ] ) ) 
    	{
    	    $notices = base64_decode( $_COOKIE[ 'ess_notices' ] );
			
    	    if ( is_serialized( $notices ) )
    	    {
        		$this->notices = unserialize( $notices );
        		setcookie(
        			'ess_notices', 
        			'', 
        			time() - 3600, 
        			COOKIEPATH, 
        			COOKIE_DOMAIN, 
        			is_ssl(), 
        			true
				);
    	    }
    	}
		add_filter( 'wp_redirect', array( &$this, 'destruct' ), 1, 1 );
    }
    
	public static function set_notices_global_handler()
	{
	    global $ESS_Notices;
	    $ESS_Notices = new ESS_Notices();	
	}
	
    public function destruct( $redirect=false )
    {
    	foreach ( $this->notices as $notice_type => $notices )
    	{
    		foreach ( $notices as $key => $notice )
    		{
    			if ( empty( $notice[ 'static' ] ) )
    				unset( $this->notices[ $notice_type ][ $key ] );
    			else
    			{
    				unset( $this->notices[ $notice_type ][ $key ][ 'static' ] ); //so it gets removed next request
    				$has_static = true;
    			}
    		}
    	}
		
        if ( count( $this->notices['errors'] 	) > 0 || 
        	 count( $this->notices['alerts'] 	) > 0 || 
        	 count( $this->notices['infos']		) > 0 || 
        	 count( $this->notices['confirms']	) > 0 )
        {
        	setcookie( 
        		'ess_notices', 
        		base64_encode( serialize( $this->notices ) ), 
        		time() + 30,  //sets cookie for 30 seconds, which may be too much
        		COOKIEPATH, 
        		COOKIE_DOMAIN, 
        		is_ssl(), 
        		true
			);
        }
    	return $redirect;
    }
    
    public function __toString()
    {
        $string = false;
		
        if ( count( $this->notices[ 'errors' ] 	) > 0 ) { $string .= "<div class='em-warning em-warning-errors error'>{$this->get_errors()}</div>"; }
        if ( count( $this->notices[ 'alerts' ]	) > 0 ) { $string .= "<div class='em-warning em-warning-alerts updated'>{$this->get_alerts()}</div>"; }
        if ( count( $this->notices[ 'infos' ] 	) > 0 ) { $string .= "<div class='em-warning em-warning-infos updated'>{$this->get_infos()}</div>";}
        if ( count( $this->notices[ 'confirms' ]) > 0 ) { $string .= "<div class='em-warning em-warning-confirms updated'>{$this->get_confirms()}</div>"; }
        
        return ( $string !== false )? "<div class='statusnotice'>" . $string . "</div>" : '';
    }
    
    private function add( $string, $type, $static = false )
    {
    	if ( is_array( $string ) ) 
    	{
    		$result = true;
			
    		foreach ( $string as $key => $string_item )
    		{
    		    if ( !is_array( $string_item ) )
    		    {
        			if ( $this->add( 	  $string_item, $type, $static ) === false )	$result = false;
    			}
    			else
    			{
        			if ( $this->add_item( $string_item, $type, $static ) === false ) 	$result = false;
    			}
    		}
    		return $result;
    	}
        
		return ( $string != '' )? $this->add_item( $string, $type, $static ) : false;
	}

    private function add_item( $string, $type, $static = false )
    {
        if ( isset( $this->notices[ $type ] ) )
        {
        	$notice_key = 0;
			
        	foreach( $this->notices[ $type ] as $notice_key => $notice )
        	{
        		if ( $string == $notice[ 'string' ] || ( is_array( $string ) && !empty( $notice[ 'title' ] ) && $this->get_array_title( $string ) == $notice[ 'title' ] ) )
        		    return $notice_key;
        	}
			
        	$i = $notice_key + 1;
			
        	if ( is_array( $string ) )
        	{
        		$this->notices[ $type ][ $i ][ 'title'  ] = $this->get_array_title( $string );
				$this->notices[ $type ][ $i ][ 'string' ] = array_shift( $string );
			}
        	else
	            $this->notices[ $type ][ $i ][ 'string' ] = $string;
        	
        	if ( $static )
        		$this->notices[ $type ][ $i ][ 'static' ] = true;
        	
        	return $i;
        }
        else
        	return false;
    }
    
    private function get_array_title( $array=NULL )
    {
    	foreach ( $array as $title => $msgs ) 
       		return $title;
    }
    
    private function remove( $key, $type )
    {
        if ( isset( $this->notices[ $type ] ) )
        {
            unset( $this->notices[ $type ][ $key ] );
            return true;
		}
        else
            return false;
    }
    
    public function remove_all()
    {
    	$this->notices = array(
    		'errors' 	=> array(), 
    		'infos'		=> array(), 
    		'alerts'	=> array(), 
    		'confirms'	=> array()
		);
    }
    
    private function get( $type )
    {
        if ( isset( $this->notices[ $type ] ) )
        {
    		$string = '';
			
            foreach ( $this->notices[ $type ] as $message )
            {
                if ( !is_array($message[ 'string' ] ) )
                    $string .= "<p>{$message['string']}</p>";
                else
                {
                    $string .= "<p><strong>". $message[ 'title' ] ."</strong><ul>";
					
                    foreach ( $message[ 'string' ] as $msg )
                    {
                        if ( trim($msg) != '' )
                        	$string .= "<li>$msg</li>";
                    } 
                    $string .= "</ul></p>";
                }
            }
            return $string;
        }
        return false;
    }
    
    private function count( $type ) 
    {
   		if ( isset( $this->notices[ $type ] ) )
    		return count( $this->notices[ $type ] );
		
        return 0;
    }
    
    /* Errors */
    public function add_error( $string, $static=false ) 	{ return $this->add( $string, 'errors', $static); }
    public function remove_error( $key ) 					{ return $this->remove( $key, 'errors' ); }
    public function get_errors() 							{ return $this->get( 'errors' ); }
    public function count_errors()							{ return $this->count('errors'); }

    /* Alerts */
    public function add_alert( $string, $static=false ) 	{ return $this->add( $string, 'alerts', $static ); }
    public function remove_alert( $key ) 					{ return $this->remove( $key, 'alerts' ); }
    public function get_alerts()							{ return $this->get( 'alerts' ); }
    public function count_alerts()							{ return $this->count( 'alerts' ); }
    
    /* Info */
    public function add_info( $string, $static=false ) 		{ return $this->add( $string, 'infos', $static ); }
    public function remove_info( $key ) 					{ return $this->remove( $key, 'infos' ); }
    public function get_infos()								{ return $this->get( 'infos' );}
    public function count_infos()							{ return $this->count( 'infos' ); }
    
    /* Confirms */
    public function add_confirm( $string, $static=false ) 	{ return $this->add( $string, 'confirms', $static ); }
    public function remove_confirm( $key )					{ return $this->remove( $key, 'confirms' ); }
    public function get_confirms()							{ return $this->get( 'confirms' ); }  
    public function count_confirms()						{ return $this->count( 'confirms' ); }

    
}	
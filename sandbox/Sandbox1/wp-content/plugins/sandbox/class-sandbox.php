<?php
class Sandbox { 
    public $shortname;
    public $name;
    public $description;
    public $prefix;
    public $dir;
    public $complete;
    
    // Returns columns for wordpress table
    public static function get_columns(){
        $columns = array(
            /*'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text*/
            'name'     => 'Name',
            'shortname' => 'Shortname',
            'description' => 'Description'
        );
            
        return $columns;
    }
    
    public static function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('name',true),     //true means its already sorted
            'shortname' => array('shortname',false),
            'description' => array('description',false)
        );
        return $sortable_columns;
    }
    
    public static function verify_parameters($action = NULL, $name = NULL, $shortname = NULL){
        global $sandboxes, $sandbox_errors;

        if($action != 'create' && $action != 'save') {
            $sandbox_error = $sandbox_errors['invalid_action'];
            $sandbox_error->add_data("Action: ".$action);
            throw new Sandbox_Exception($sandbox_error);
        }
        if(empty($name)) throw new Sandbox_Exception($sandbox_errors['no_name']);
        if(empty($shortname)) throw new Sandbox_Exception($sandbox_errors['no_shortname']);
        if($action == 'create' && isset($sandboxes[$shortname])) throw new Sandbox_Exception ($sandbox_errors['inuse_shortname']);
        if(!preg_match('/^[a-zA-Z1-9]+$/', $shortname)) throw new Sandbox_Exception($sandbox_errors['bad_shortname']);
        foreach($sandboxes as $sandbox){
            
        }
    }
    
    public function __construct($name = NULL, $shortname = NULL, $description) {
        global $sandbox_dir, $sandbox_errors;
        Sandbox::verify_parameters('create', $name, $shortname);
        $this->name = $name;
        $this->shortname = $shortname;
        $this->description = $description;
        $this->dir = $sandbox_dir.$this->shortname."/";
        $this->prefix = $this->table_prefix();
        
    }
    
    public function assoc(){
        $assoc_sandbox = get_object_vars($this);
        return $assoc_sandbox;
    }
    
    public function create(){
        global $sandboxes;
        $this->print_header("Creating Sandbox - ".$this->shortname);
        $this->complete = false;
        
        try {
            // Add sandbox to options before create so that sandbox is self aware
            $sandboxes[$this->shortname] = $this;
            update_option("sandboxes", $sandboxes);
              
            $this->print_status_header("Creating tables");
            $this->copy_tables();
            $delete_tables = TRUE;

            $this->print_status_header("Copying files"); 
            $this->copy_files();
            $remove_files = TRUE;

            $this->print_status_header("Updating sandbox wp-config.php");
            $this->update_wp_config();
            
            $this->print_status_header("Sandbox created without error.");
            $this->print_footer("<a href='".admin_url('admin.php?page=sandbox&action=activate&shortname='.$this->shortname)."'>Activate ".$this->name."</a> ");
            
            $this->complete = true;
            $sandboxes[$this->shortname] = $this;
            update_option("sandboxes", $sandboxes);
            
            return true;
        } catch (Sandbox_Exception $sandbox_exception) {          
            $sandbox_exception->sandbox_error->print_error();
            
            //Insure Sandbox is unset from save
            unset($sandboxes[$this->shortname]);
            update_option("sandboxes", $sandboxes);
            
            // Clean up files for successful steps
            if($delete_tables) {
                $this->print_status_header("Performing clean-up of tables");
                $this->delete_tables();
            }
            if($remove_files) {
                $this->print_status_header("Performing clean-up of files"); 
                $this->delete_files();
            }

            $this->print_status_header("Sandbox creation halted with error.");
            $this->print_footer();
            
            return false;
        }
    }
    
    public function delete($verified = false){
        global $sandboxes;
        if($verified){
            try {
                $this->print_header("Deleting Sandbox - ".$this->shortname);

                $this->print_status_header("Deleting tables");
                $this->delete_tables(true);

                $this->print_status_header("Deleting files"); 
                $this->delete_files(true);

                $this->print_status_header("Sandbox deleted without error.");
                $this->print_footer();
                
                unset($sandboxes[$this->shortname]);
                update_option('sandboxes', $sandboxes);

                return true;
            } catch (Sandbox_Exception $sandbox_exception) {
                 $sandbox_exception->sandbox_error->print_error();
                 $this->print_status_header("Sandbox deletion halted with error.");
                 $this->print_footer();

                 return false;
            }
        } else {
            try {
                $this->print_header("Verify Sandbox Deletion - ".$this->shortname);

                $this->print_status_header("Deleting tables");
                $this->delete_tables();

                $this->print_status_header("Deleting files"); 
                $this->delete_files();

                $this->print_status_header("Continuation with sandbox deletion will perform the above actions and is not reversible.");
                $this->print_footer("<a href='".admin_url('admin.php?page=sandbox&action=delete_verified&shortname='.$this->shortname)."'>Confirm Deletion of ".$this->name."</a> ");

                return true;
            } catch (Sandbox_Exception $sandbox_exception) {
                 $sandbox_exception->sandbox_error->print_error();
                 $this->print_status_header("Sandbox deletion halted with error.");
                 $this->print_footer();

                 return false;
            }
        }
    }
    
    private function print_header($string){
        echo "<h2>".$string."</h2>";
        
        wp_ob_end_flush_all();
        flush();
    }
    
    private function print_footer($links = ""){
        if(!empty($links)) $links .= " | ";
        echo $links."<a href='".admin_url('admin.php?page=sandbox')."'>Return to Sandboxes</a> | <a id='show_details_link'>Show Details</a><a id='hide_details_link'>Hide Details</a>";
        
        ?>
        <script type="text/javascript">
            jQuery(function($) {
                $("#show_details_link").click(function (e){
                    $('.sandbox_output_detailed').show();
                    $('#show_details_link').hide();
                    $('#hide_details_link').show();
                });

                $("#hide_details_link").click(function (e){
                    $('.sandbox_output_detailed').hide();
                    $('#show_details_link').show();
                    $('#hide_details_link').hide();
                });
            });
        </script>
        <?php
        
        wp_ob_end_flush_all();
        flush();
    }
    
    private function print_status_header($string){
        echo "<h4>".$string."</h4>";
        wp_ob_end_flush_all();
        flush();
    }
    
    private function print_status($string){
        echo "<div style='display: none' class='sandbox_output_detailed'>".$string."<br /></div>";
        wp_ob_end_flush_all();
        flush();
    }
    
    private function print_notice($string){
        print '<div class="updated">
            <p>'.$string."</p>\n</div>";
    }
    
    private function copy_tables($schema = NULL){
        global $wpdb, $sandboxes, $sandbox_errors;
        
        if(empty($this->prefix)) throw new Sandbox_Exception($sandbox_errors['no_prefix']);
        
        $tables = sandbox_get_results("SHOW TABLES LIKE '".$wpdb->base_prefix."%'", ARRAY_N, 'no_table_list');
        $wp_tables = array();
        foreach($tables as $table){
            $wp_tables[] = $table[0];
        }

        $tables = sandbox_get_results("SHOW TABLES", ARRAY_N, 'no_table_list');
        $all_tables = array();
        foreach($tables as $table) $all_tables[] = $table[0];
        
        // Do as much error handling up front to avoid partially creating database.
        $create_tables = array();
        foreach($all_tables as $table){
            $other_sandbox_table = FALSE;
            
            // Check if this sandbox prefix is part of existing table, could result in deleted table 
            // outside of sandbox when sandbox is removed
            if(preg_match('/^'.$this->prefix.'/', $table)){
                $sandbox_error = $sandbox_errors['prefix_exists'];
                $sandbox_error->add_data("Table: ".$sandbox_table);
                throw new Sandbox_Exception($sandbox_error);
            }
                        
            foreach($sandboxes as $sb){
                if(preg_match('/^'.$sb->prefix.'/', $table)) $other_sandbox_table = TRUE;
            }  

            if(!$other_sandbox_table){
                // Check if part of live wordpress installation
                if(in_array($table, $wp_tables)){
                    $sandbox_table = preg_replace('/^'.$wpdb->base_prefix."/", $this->prefix, $table, 1, $count);
                    // Check if potential sandbox table, already exists
                    if(in_array($sandbox_table, $all_tables)){
                        $sandbox_error = $sandbox_errors['table_exists'];
                        $sandbox_error->add_data("Table: ".$sandbox_table);
                        throw new Sandbox_Exception($sandbox_error);
                    } else {
                        $create_tables[$table] = $sandbox_table;
                    }
                } else {
                    $this->print_notice("'".$table."' table is not part of a sandbox or Wordpress. "
                            . "This table will not be duplicated and could potentially be written to by a sandbox. <br/><b>Use sandboxes with caution!</b>");
                }
            }
            
            // Perform this last test to insure no existing tables would be deleted with new prefix. wp
            if(preg_match('/^'.$this->prefix.'/', $table)) {
                $sandbox_error = $sandbox_errors['table_prefix_match'];
                $sandbox_error->add_data("Table: ".$table);
                throw new Sandbox_Exception($sandbox_error);
            }
        }
        
        if(empty($create_tables)) throw new Sandbox_Exception($sandbox_errors['no_tables_found']);
        
        

        if(empty($schema)){
            // If schema empty, copy tables to new table names <prefix>_<shortname_<tablename>
            foreach($create_tables as $live_table => $sandbox_table){
                $this->print_status("Creating ".$sandbox_table);
                sandbox_query("CREATE TABLE ".$sandbox_table." LIKE ".$live_table, 'create_table');
                sandbox_query("INSERT INTO ".$sandbox_table." SELECT * FROM ".$live_table, 'insert_table');
            }
            
            // Update table data
            $this->print_status("Updating table data with prefix changes.");
            sandbox_query("UPDATE `".$this->prefix."options` SET option_name = '".$this->prefix."user_roles' WHERE option_name = 'wp_user_roles'", 'update_options');
            $substr_cnt = strlen($wpdb->prefix) + 1;
            sandbox_query("UPDATE `".$this->prefix."usermeta` SET meta_key = concat('".$this->prefix."', substring(meta_key, ".$substr_cnt.")) WHERE meta_key like '".$wpdb->base_prefix."%'", 'update_usermeta');            
        } else {
            // If schema is provided, copy tables to schema and use existing table names
        }
    }
    
    private function delete_tables($verified = FALSE, $schema = NULL){
        global $wpdb, $sandboxes, $sandbox_errors;
        //Debug
        $wpdb->show_errors();
        
        if(empty($this->prefix)) throw new Sandbox_Exception($sandbox_errors['no_prefix']);
        
        $tables = $wpdb->get_results("SHOW TABLES LIKE '".$this->prefix."%'", ARRAY_N);
        // Add check for tables that do not have a prefix
        
        if(empty($schema)){
            // If schema empty, copy tables to new table names <prefix>_<shortname_<tablename>
            foreach($tables as $sandbox_table){
                $sandbox_table = $sandbox_table[0]; 
                $this->print_status("Delete ".$sandbox_table);
                if($verified) $wpdb->query("DROP TABLE ".$sandbox_table);
            }
        } else {
            // If schema is provided, copy tables to schema and use existing table names
        }
    }
    
    private function copy_files(){
        global $wp_dir, $sandbox_dir, $sandbox_errors;
        
        if(!file_exists($sandbox_dir)){
            $sandbox_error = $sandbox_errors['no_main_sandbox_dir'];
            $sandbox_error->add_data("Directory: ".$sandbox_dir);
            throw new Sandbox_Exception($sandbox_error );
        } 
        $paths = $this->recursive_listing($wp_dir, array($sandbox_dir));
        
        // Make Directory for sandbox
        if(file_exists($this->dir)){
            throw new Sandbox_Exception($sandbox_errors['sandbox_folder_exists']);
        } else {
            mkdir($this->dir, 0755);
        }
        
        
        // Loop through paths and copy to sandbox
        foreach($paths as $from_path){
            $to_path = str_replace($wp_dir,$this->dir,$from_path);
            if(file_exists($to_path)){
              $sandbox_error = $sandbox_errors['sandbox_file_exists'];
              $sandbox_error->add_data("File: ".$to_path);
              throw new Sandbox_Exception($sandbox_error);
            }
            if(is_dir($from_path)){
                $this->print_status("Creating directory ".$to_path);
                mkdir($to_path, 0755);
            } else {
                $this->print_status("Copying live file to ".$to_path);
                copy($from_path, $to_path);
            }
        }
    }
    
    private function delete_files($verified = FALSE){
        global $wp_dir, $sandbox_dir;
        
        $paths = $this->recursive_listing($this->dir);
        arsort($paths);
        // Loop through paths and delete
        foreach($paths as $path){
            if(is_dir($path)){
                $this->print_status("Remove directory ".$path);
                if($verified) rmdir($path);
            } else {
                $this->print_status("Remove file ".$path);
                if($verified) unlink($path);
            }
        }
        
        if(file_exists($this->dir)){
            $this->print_status("Remove sandbox directory ".$this->dir);
            if($verified) rmdir($this->dir);
        }
    }
    
    private function update_wp_config(){
        global $wpdb;
        
        $this->print_status("Setting table prefix to ".$this->prefix);
        $wp_config = file($this->dir."wp-config.php");
        $new_wp_config = array();
        foreach($wp_config as $line){
            // Remove after comment hash
            $search_line = current(explode('#', $line));
            if(preg_match('/\$table_prefix/', $search_line)){
                $reg_ex = '/(.*\$table_prefix\s*=\s*[\'"]+)'.$wpdb->prefix.'([\'"]+.*)/'; 
                $rep_str = '$1'.$this->prefix.'$2';
                $new_line = preg_replace($reg_ex, $rep_str, $line);
                $new_wp_config[] = $new_line;
            } else {
                $new_wp_config[] = $line;
            }
        }
        
        file_put_contents($this->dir."wp-config.php", implode("\n", $new_wp_config));
    }
    
    private function recursive_listing($dir, $ignore = array()){ 
        if ($handle = opendir($dir)) {
            $paths = array();
            while (false !== ($node = readdir($handle))) {
                $path = $dir.$node;
                if(is_dir($path))
                    $path = $path."/";
                if(!is_link($path) && $node != '.' && $node != '..' && !in_array($path, $ignore)){
                    $paths[] = $path;
                    if(is_dir($path)){
                       $sub_files = $this->recursive_listing($path, $ignore);
                       $paths = array_merge($paths, $sub_files);
                    } 
                }
                
            }
        }
        return $paths;
    }
    
    private function table_prefix(){
        global $wpdb;
        
        if(preg_match('/([-_])$/', $wpdb->base_prefix, $matches)){
            return $wpdb->base_prefix.strtolower($this->shortname).$matches[1];
        } else {
            return $wpdb->base_prefix.'_'.strtolower($this->shortname).'_';
        }
    }
}
?>
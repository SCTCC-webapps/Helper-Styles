<?php
class Helper
{


	//Setting 0 for widths assumes you want the full-screen-size.

	//Variable for how wide we want the form.
	public $form_width_small = 'col-lg-4 col-md-6 col-sm-8 col-lg-offset-4 col-md-offset-3 col-sm-offset-2';
	public $form_width_medium = 'col-lg-6 col-md-6 col-sm-8 col-lg-offset-3 col-md-offset-3 col-sm-offset-2';
	public $form_width_large = 'col-lg-12 col-md-12 col-sm-12';

	//Variable for which way we want the page oriented, vertical or horizontal.
	public $orientation = 'h';

	//Variable for label-width. Standard is 2.
	public $label_width = '2';

	//Variable for input width.
	public $input_width = 0;

	//Variable for textareas and other inputs that may require a height parameter.
	public $input_height = 0;

	//If one wants to make an input readonly.
	//public $input_readonly = 0;

	//Bootstrap flag. Do we want to use the bootstrap syntax or standard html?
	public $input_bootstrap = 0;

	public $parameters = array(
		'input_name' => '',
		'input_value' => '',
		'label_name' => '',
		'placeholder' => '',
		'readonly' => '',
		'default_selected' => '',
		'checked' => '',
		'class' => ''
	);

	public $parameter_select_values = array();


	/**
	 * @param $array array :: Assigning all html variables within an array and passing it in.
	 * Bootstrap html helper function. (Vertical forms, more mobile-friendly)
	 */
	function display_text(array $array)
	{
		$array = $this->fill_placeholder_array($array, $this->parameters);

		$variables = $this->grab_class_variables();

		if($array['label_name']) $this->label_helper($array['input_name'], $array['label_name']);

		//Checking if width is set. If it is, add a class to it defining its width.
		echo "
        <div"; if($variables['bootstrap'] && $variables['width']) echo" class='col-lg-".$variables['width']." col-md-".$variables['width']." col-sm-".$variables['width']."'"; echo">
            <input "; if($variables['bootstrap']) echo" class='".$array['class']." form-control'
            type='text' 
            placeholder='" . $array['placeholder'] .
		"' name='" . $array['input_name'] .
		"' id='" . $array['input_name'] .
		"' value='" . $array['input_value'] . "'";
		if($array['readonly']) echo " readonly ";
		echo"/>
        </div>";
	}

	function display_hidden(array $array){
		$array = $this->fill_placeholder_array($array, $this->parameters);

		echo "<input type='hidden' name='".$array['input_name']."' value='".$array['input_value']."' />";
	}

	/**
	 * @param $parameters array :: The settings the user is sending in.
	 * @param $array_names array :: This is what is going to be displayed.
	 * @param $array_values array :: (Initializing it to null makes the "optional") If this is sent in, it'll be our customized values for the select list. If not sent in, we share the values from the display array.
	 */
	function display_select($parameters, $array_names, $array_values = null)
	{
		$array = $this->fill_placeholder_array($parameters, $this->parameters);
		$variables = $this->grab_class_variables();
		if($array['label_name']) $this->label_helper($array['input_name'], $array['label_name']);

		echo "<div"; if($variables['width'] && $variables['bootstrap']) echo" class='col-lg-".$variables['width']." col-md-".$variables['width']." col-sm-".$variables['width']."'"; echo">";
		echo "<select name='".$array['input_name']."' id='".$array['input_name']."'"; if($variables['bootstrap']) echo" class='form-control'"; echo">";
		//Setting placeholder, if someone set it.
		//if($array['default_selected']) echo "<option selected='selected' "; if($array['default_selected']=='Select one') echo "value='-1'"; else echo "value='".$array['default_selected']."'";
		if($array['default_selected']) echo "<option selected='selected' value='-1'";
		echo">".$array['default_selected']."</option>";


		//If the user supplied their own keys to use, look them up and supply them in the <option>, else, just re-use the label as the key.
		if($array_values){
			for($i = 0; $i < count($array_names); $i++){
				$key = array_search($array_names[$i], $array_values);
				echo "<option value='".$key."'>".$array_names[$i]."</option>";
			}
		}else{
			foreach($array_names as $value){
				echo "<option value='".$value."'>".$value."</option>";
			}
		}

		echo "</select>";
		echo "</div>";
	}

	//Some display issues if not done inline
	function display_radio(array $array, array $values)
	{
		$array = $this->fill_placeholder_array($array, $this->parameters);
		$variables = $this->grab_class_variables();
		if($array['label_name']) $this->label_helper($array['input_name'], $array['label_name']);


		foreach($values as $row){
			if(ucfirst($this->orientation) == 'V') echo "<div class='radio'>";
			echo "<label"; if(ucfirst($this->orientation) == 'H') echo ' class="radio-inline"'; echo"><input type='radio' name='".$array['input_name']."' value='".$row."'>".$row."</label>";
			if(ucfirst($this->orientation) == 'V') echo "</div>";
		}
	}

	/**
	 * @param $array array :: parameters array.
	 */
	function display_checkbox(array $array)
	{
		$array = $this->fill_placeholder_array($array, $this->parameters);

		$variables = $this->grab_class_variables();

		echo'
           <div '; if($variables['width']) echo'class="col-lg-'.$variables['width'].' col-md-'.$variables['width'].' col-sm-'.$variables['width'].' padded-light"'; echo'>
               <label class="checkbox-inline pull-left"><input type="checkbox" name="'.$array['input_name'].'" id="'.$array['input_name'].'" value="'.$array['input_value'].'"'; if($array['checked']) echo'checked'; echo'>'.$array['label_name'].'</label>
           </div>
           ';
	}

	function display_textarea(array $array)
	{
		$array = $this->fill_placeholder_array($array, $this->parameters);

		$variables = $this->grab_class_variables();

		if($array['label_name']) $this->label_helper($array['input_name'], $array['label_name']);

		echo "
        <div"; if($variables['bootstrap'] && $variables['width']) echo" class='col-lg-".$variables['width']." col-md-".$variables['width']." col-sm-".$variables['width']."'"; echo">
            <textarea "; if($variables['bootstrap']) echo" class='form-control' "; echo" name='".$array['input_name']."' "; if($variables['height']) echo" rows='".$variables['height']."'"; echo" id='".$array['input_name']."'"; if($array['readonly']) echo " readonly";
		echo">".$array['input_value']."</textarea>
        </div>";
	}

	//Helper function that prints out table cells by calling another function within the class.
	function display_table_row_input(array $array)
	{
		$array = $this->fill_placeholder_array($array, $this->parameters);

		echo "
            <td>";
		$this->display_text(
			array(
				'input_name' => $array['input_name'],
				'input_value' => $array['input_value'],
				'placeholder' => $array['placeholder']
			)
		);
		echo "</td>
        ";
	}

	/**
	 * @param $input_name string :: Name of input the label is for.
	 * @param $label_name string :: Text of the label.
	 * This is a helper function to our other functions that test if label text has been set.
	 */
	function label_helper($input_name, $label_name)
	{
		$variables = $this->grab_class_variables();

		//If width is 0, default to max width.
		if($variables['label_width']) {
			echo "<label ";
			if ($variables['bootstrap']) echo "class='control-label col-lg-" . $variables['label_width'] . " col-md-" . $variables['label_width'] . " col-sm-" . $variables['label_width'] . "'";
			echo " for='" . $input_name . "'>" . $label_name . ":</label>";
		}else{
			echo "<label ";
			if ($variables['bootstrap']) echo "class='col-lg-12 col-md-12 col-sm-12'";
			echo " for='" . $input_name . "'>" . $label_name . ":</label>";
		}


	}

	/**
	 * @param $array1 array :: Array 1 with values.
	 * @param $array2 array :: Array with null values.
	 * @return array
	 */
	function fill_placeholder_array($array1, $array2){
		foreach($array2 as $key => $value){
			if(array_key_exists($key, $array1)){
				$array2[$key] = $array1[$key];
			}
		}
		return $array2;
	}


	function semester_select()
	{
		$this_year = date('Y');
		$future_date = date('Y', strtotime('+1 year'));
		$past_date = date('Y', strtotime('-1 year'));

		$semesters = array
		(

		);

		array_push($semesters, 'Spring ' . $past_date);
		array_push($semesters, 'Summer ' . $past_date);
		array_push($semesters, 'Fall ' . $past_date);

		array_push($semesters, 'Spring ' . $this_year);
		array_push($semesters, 'Summer ' . $this_year);
		array_push($semesters, 'Fall ' . $this_year);

		array_push($semesters, 'Spring ' . $future_date);
		array_push($semesters, 'Summer ' . $future_date);
		array_push($semesters, 'Fall ' . $future_date);

		return $semesters;
	}

	//A helper class for our functions that pull in all possibly-set values from the user and assign them in an associative array.
	function grab_class_variables()
	{
		$variables = array();

		$width = $this->input_width;
		$variables['width'] = $width;

		$label_width = $this->label_width;
		$variables['label_width'] = $label_width;

		//$readonly = $this->input_readonly;
		//$variables['readonly'] = $readonly;

		$orientation = $this->orientation;
		$variables['orientation'] = $orientation;

		$height = $this->input_height;
		$variables['height'] = $height;

		$bootstrap = $this->input_bootstrap;
		$variables['bootstrap'] = $bootstrap;

		return $variables;
	}

	function dynamic_sql_insert($table_name)
	{
		global $dbh;

		$sql = "INSERT INTO " . $table_name . " SET ";

//print_r($_POST);

		$size = count($_POST);
//echo "<br/>" . $size . "<br />";
		//array_count_values will default have the size of the array in as a value, so we need to account for that.
		foreach ($_POST as $key => $value) {
			$size--;
			//If item is not the last in array, comma-separate the list.
			if ($size > 0) {
				$sql .= $key . " = :" . $key . ", ";
			} else {
				$sql .= $key . " = :" . $key;
			}
		}

//echo "<br/>". $this->showQuery($sql, $_POST)."<br/>";


		//The reason for two foreach loops is that we need our prepare statement before we bind.
		//We cannot have the prepare fire off for every single key/value pair.
		$sth = $dbh->prepare($sql);

		foreach ($_POST as $key => $value) {
			//Do not include submit button.
			//if($value == 'Submit application') continue;

			if (!is_null($value) && $value != '') {
				$sth->bindValue(':' . $key, $value);
			}else{
				$sth->bindValue(':' . $key, null, PDO::PARAM_INT);
			}
		}
//echo "<br />" . $sql . "<br />";
		$sth->execute();
	}

	function dynamic_sql_update($table_name, $where_field, $where_value)
	{
		global $dbh;

		$sql = "UPDATE " . $table_name . " SET ";

//print_r($_POST);
		//We are counting the total amount of set variables in the $_POST.
		$size = count($_POST);
		//array_count_values will default have the size of the array in as a value, so we need to account for that.
		//if($size > 1) {
		foreach ($_POST as $key => $value) {
			//Do not include submit button.
			if($value == 'Submit application'){
				$size--;
				continue;
			}

			$size--;
			//If item is not the last in array, comma-separate the list.
			if ($size > 0) {
				$sql .= $key . " = :" . $key . ", ";
			} else {
				$sql .= $key . " = :" . $key;
			}
		}
		//Where clause
		$sql .= " WHERE " . $where_field . " = :where_value";
		//echo $this->showQuery($sql, $_POST);
		//echo $where_value;

//echo $sql;

		//The reason for two foreach loops is that we need our prepare statement before we bind.
		//We cannot have the prepare fire off for every single key/value pair.
		$sth = $dbh->prepare($sql);

		$sth->bindValue(':where_value', $where_value);

		foreach ($_POST as $key => $value) {
			//Do not include submit button.
			if($key == 'Submit application') continue;

			//date-format the proper fields
			if($key == 'requested_date'){
				$value = date('Y-m-d', strtotime($value));
			}

			$dbh->quote($value);

			//If it has a value. Else, specify it as a null value.
			if (!is_null($value) && $value != '') {
				$sth->bindValue(':' . $key, $value);
			}else{
				$sth->bindValue(':' . $key, null, PDO::PARAM_INT);
			}
		}

		$sth->execute();
		//}

//echo "<p>".$sql."</p>";
	}

	/**
	 * @param $query Your $sql statement.
	 * @param $params An array. I generally pass the $_POST[] array, but to add $_GET[], etc, you will need to make your own and pass that through.
	 * @return Returns what a PDO SQL statement may look like after binding.
	 * Helper function to simulate query.
	 */
	function showQuery($query, $params)
	{
		$keys = array();
		$values = array();

		# build a regular expression for each parameter
		foreach ($params as $key => $value) {
			if (is_string($key)) {
				$keys[] = '/:' . $key . '/';
			} else {
				$keys[] = '/[?]/';
			}

			if (is_numeric($value)) {
				$values[] = intval($value);
			} else {
				$values[] = '"' . $value . '"';
			}
		}

		$query = preg_replace($keys, $values, $query, 1, $count);
		return $query;
	}

	//Reads an array and assigns them as public class variables.
	function read_local_vars(array $v){
		for($i = 0; $i < count($v); $i++)
		{
			//Ending after first space, so this will get the variable name.
			$variable_name = substr($v[$i], 0, strpos($v[$i], ' '));
			$this->$variable_name = '';
		}

	}

}
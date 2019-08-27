<form action="employees.php" method="POST" >
	<table>
		<tr>
			<td>Action</td>
			<td><select name="action" id="action">
					<option selected="selected" value="READ">READ</option>
					<option value="CREATE">CREATE</option>
					<option value="READALL">READALL</option>
					<option value="UPDATE">UPDATE</option>
					<option value="DELETE">DELETE</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Emp ID</td>
			<td><input type="text" id="empid" name="empid" maxlength="5"></td>
		</tr>
		<tr>
			<td>Emp Name</td>
			<td><input type="text" id="ename" name="ename" maxlength="40"></td>
		</tr>

		<tr>
			<td>DOB YYYY-MM-DD</td>
			<td><input type="text" id="dob" name="dob" maxlength="10"></td>
		</tr>
		
		<tr>
			<td>Email</td>
			<td><input type="text" id="email" name="email" maxlength="40"></td>
		</tr>
		
		<tr>
			<td>Mobile</td>
			<td><input type="text" id="mobile" name="mobile" maxlength="10"></td>
		</tr>
		
		<tr>
			<td>Address (Max 300 chars)</td>
			<td><textarea type="text" id="addr" name="addr" rows="5" cols="60"></textarea></td>
		</tr>

		<tr>
			<td>Location</td>
			<td>
				Latt: <input type="text" id="latt" name="latt" maxlength="10">
				Long: <input type="text" id="long" name="long" maxlength="10">

			</td>
		</tr>

		<tr>
			<td>Profile PIC (base64 jpg)</td>
			<td><textarea id="profilepic" name="profilepic"></textarea></td>
		</tr>
		<tr>
			<td colspan="2" align="center"><input type="submit" value="submit"></td>
		</tr>
	</table>
</form>
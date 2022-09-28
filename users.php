<?php

public function updateUsers($users)
{
	foreach ($users as $user) {

		try {
			if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10)
				DB::table('users')->where('id', $user['id'])->update([
					'name' => $user['name'],
					'login' => $user['login'],
					'email' => $user['email'],
					'password' => md5($user['password'])
				]);
		} catch (\Throwable $e) {
			return Redirect::back()->withErrors(["error", ["We couldn't update user: " . $e->getMessage()]]);
		}
	}
	return Redirect::back()->with(["success", "All users updated."]);
}

public function storeUsers($users)
{

	$userArray = array();
    foreach ($users as $user) {

    	// instead of doing insertion inside a loop, we can do batch insertion.

    	if ($user['name'] && $user['login'] && $user['email'] && $user['password'] && strlen($user['name']) >= 10){

	    	$userArray[] = array('name' => $user['name'],
								 'login' => $user['login'],
								 'email' => $user['email'],
								 'password' => md5($user['password'] );
		}
    }
    if (!empty($userArray)) {
	    try {	    		
			DB::table('users')->insert($userArray);

	    } catch (\Throwable $e) {
	        return Redirect::back()->withErrors(["error", ["We couldn't store user: " . $e->getMessage()]]);
	    }
	    $this->sendEmail($users);
	    return Redirect::back()->with(["success", "All users created."]);
	}else{
		return Redirect::back()->with(["msg", "No user details found"]);
	}
}

private function sendEmail($users)
{
    foreach ($users as $user) {
        $message = "Account has beed created. You can log in as <b>" . $user['login'] . "</b>";
        if ($user['email']) {
            Mail::to($user['email'])
                ->cc('support@company.com')
                ->subject('New account created')
                ->queue($message);
        }
    }
    return true;
}

?>
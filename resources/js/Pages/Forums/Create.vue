<!--
session_start();
$logID = $_SESSION['loggedID'] ?? $_COOKIE['loggedID'] ?? null;
$logUser = $_SESSION['loggedUser'] ?? $_COOKIE['loggedUser'] ?? null;
$logUserRole = $_SESSION['loggedUserRole'] ?? $_COOKIE['loggedUserRole'] ?? null;

if (!isset($logID)) {
	header("Location: ../user/login.php");
	exit();
}
?>

// REQUIRED FOR REFERENCE LATER

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (empty($_POST['title']) || empty($_POST['description'])) {
			echo "Please fill in missing fields.<br>";
		} else {
			if (!empty($_FILES['img']['name'])) {
				$image_name = $_FILES["img"]["name"];
				$image_tmp = $_FILES["img"]["tmp_name"];
				$image_folder = "../resource/img/post/";
				$image_path = $image_folder . $image_name;

				if (!file_exists($image_folder)) {
					mkdir($image_folder, 0775, true);
				}

				if (move_uploaded_file($image_tmp, $image_path)) {
					include('../resource/conn.php');

					$stmt1 = $conn->prepare("INSERT INTO Post (title, description, postPhoto, userID)
                        VALUES (:title, :description, :postPhoto, :userID)  ");
					$stmt1->bindParam(':title', $_POST['title']);
					$stmt1->bindParam(':description', $_POST['description']);
					$stmt1->bindParam(':postPhoto', $image_name);
					$stmt1->bindParam(':userID', $logID);
					$stmt1->execute();
				} else {
					echo "Something went wrong while uploading the image. Please try again.";
				}
			} else {
				include('../resource/conn.php');

				$stmt1 = $conn->prepare("INSERT INTO Post (title, description, userID)
                        VALUES (:title, :description, :userID)  ");
				$stmt1->bindParam(':title', $_POST['title']);
				$stmt1->bindParam(':description', $_POST['description']);
				$stmt1->bindParam(':userID', $logID);
				$stmt1->execute();
			}
			$stmt2 = $conn->prepare("SELECT Post.postID FROM Post JOIN User ON Post.userID = User.userID WHERE Post.userID = :userID ORDER BY Post.postID DESC LIMIT 1");
			$stmt2->bindParam(':userID', $logID);
			$stmt2->execute();
			$newPost = $stmt2->fetch(PDO::FETCH_ASSOC);
			$postID = $newPost['postID'];
			header("Location: post.php?postID=$postID");
		}

		$conn = null;
	}
-->

<template>
    <h1 class="text-4xl font-bold m-4">Create New Post</h1>
    <Card>
        <template #content>
            <form id="form" @submit.prevent="onSubmit()" method="post">
                <div class="flex flex-col gap-2">
                    <div class="flex flex-col">
                    <label for="title">Title<span style="color: red;">*</span></label>
                    <InputText v-model="form.title" placeholder="Enter your title..." style="width: 100%;" required />
                    </div>

                    <div class="flex flex-col">
                        <label for="description">Description<span style="color: red;">*</span> </label>
                        <Textarea v-model="form.description" placeholder="Enter your description..." rows="5" style="width: 100%; resize:none;" required />
                    </div>

                    <div class="flex flex-col">
                        <label for="img">Add Image (optional):</label>
                        <FileUpload v-model="form.img" chooselabel="Browse" accept="image/*" />
                    </div>
                </div>
            </form>
        </template>
        <template #footer>
            <Button type="submit" label="Create Post" form="form" class="float-right">
                <template #icon>
                    <Plus />
                </template>
            </Button>
        </template>
    </Card>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

import Button from 'primevue/button'
import Card from 'primevue/card'
import FileUpload from 'primevue/fileupload'
import InputText from 'primevue/inputtext'
import Textarea from 'primevue/textarea'

import Plus from '@primeicons/vue/plus'

const form = useForm({
    title: '',
    description: '',
    img: null
})

const onSubmit = () => {
    form.post('/forum/post/store')
}
</script>


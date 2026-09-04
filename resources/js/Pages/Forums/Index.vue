<!--
session_start();
include('../resource/conn.php');
$logID = $_SESSION['loggedID'] ?? $_COOKIE['loggedID'] ?? null;
$logUser = $_SESSION['loggedUser'] ?? $_COOKIE['loggedUser'] ?? null;
$logUserRole = $_SESSION['loggedUserRole'] ?? $_COOKIE['loggedUserRole'] ?? null;

$limit = 20;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Set parameters for searching
$search = isset($_GET['search']) ? "%" . $_GET['search'] . "%" : "%%"; // default to wildcard
$searchType = $_GET['searchType'] ?? 'post'; // default to post
$sortType = $_GET['sortType'] ?? 'latest';   // default to latest
$isSearching = $_SERVER["REQUEST_METHOD"] === "GET" && (isset($_GET['search']) || isset($_GET['sortType']));

if ($searchType === 'user') {
    // Determine how to sort
    switch ($sortType) {
        case 'upvote':
            $orderBy = "totalUpvotes DESC";
            break;
        case 'alphabet':
            $orderBy = "username ASC";
            break;
        case 'latest':
        default:
            $orderBy = "regDate DESC";
            break;
    }

    // Count total users matching search (for pagination)
    $totalStmt = $conn->prepare("SELECT COUNT(*) FROM User WHERE username LIKE :search");
    $totalStmt->bindParam(':search', $search, PDO::PARAM_STR);
    $totalStmt->execute();
    $totalCount = $totalStmt->fetchColumn();
    $totalPages = ceil($totalCount / $limit);

    // Fetch user info + upvotes from posts and comments
    $stmt = $conn->prepare("
        SELECT User.userID, User.username, User.userPhoto, User.regDate, User.role,
            COALESCE(PostUp.postUpvotes, 0) AS postUpvotes,
            COALESCE(CommUp.commentUpvotes, 0) AS commentUpvotes,
            COALESCE(PostUp.postUpvotes, 0) + COALESCE(CommUp.commentUpvotes, 0) AS totalUpvotes
        FROM User
        LEFT JOIN (
            SELECT Post.userID, COUNT(Upvote.contentNo) AS postUpvotes
            FROM Post
            LEFT JOIN Upvote ON Upvote.contentNo = Post.postID
            GROUP BY Post.userID
        ) PostUp ON PostUp.userID = User.userID
        LEFT JOIN (
            SELECT Comment.userID, COUNT(Upvote.contentNo) AS commentUpvotes
            FROM Comment
            LEFT JOIN Upvote ON Upvote.contentNo = Comment.commentID
            GROUP BY Comment.userID
        ) CommUp ON CommUp.userID = User.userID
        WHERE User.username LIKE :search
        ORDER BY $orderBy
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
} else {
    switch ($sortType) {
        case 'upvote':
            $orderBy = "upvotes DESC";
            break;
        case 'alphabet':
            $orderBy = $searchType === 'user' ? "username ASC" : "title ASC";
            break;
        case 'latest':
        default:
            $orderBy = $searchType === 'user' ? "regDate DESC" : "Post.postID DESC";
            break;
    }

    // Total count for pagination
    $totalStmt = $conn->prepare("SELECT COUNT(*) FROM Post WHERE title LIKE :search");
    $totalStmt->bindParam(':search', $search, PDO::PARAM_STR);
    $totalStmt->execute();
    $totalCount = $totalStmt->fetchColumn();
    $totalPages = ceil($totalCount / $limit);

    // Data query with upvote JOIN
    $stmt = $conn->prepare("
        SELECT Post.postID, Post.title, Post.description, Post.postDate, User.username, COUNT(Upvote.contentNo) AS upvotes
        FROM Post
        LEFT JOIN User ON Post.userID = User.userID
        LEFT JOIN Upvote ON Upvote.contentNo = Post.postID
        WHERE Post.title LIKE :search
        GROUP BY Post.postID
        ORDER BY $orderBy
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
}

// Apply pagination
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
$conn = null;


// SNIPPETS FROM OLD HTML AS REFERENCE

<?php if (isset($_GET['searchType']) && $_GET['searchType'] == "user"): ?>
                <?php foreach ($results as $row): ?>
                    <div class="container border">
                        <img src="../resource/img/user/<?= htmlspecialchars($row['userPhoto']) ?>"
                                onerror="this.onerror=null; this.src='../resource/img/user/placeholder.png'"
                                class="img-thumbnail rounded-circle float-sm-left mr-3"
                                style="width: 100px; height: 100px;">
                        <a href='../user/profile.php?userID=<?= $row['userID'] ?>'>
                            <h3><?= $row['username'] ?></h3>
                        </a>
                        <p><?= $row['role'] ?></p>
                        <p>Member since <?= $row['regDate'] ?><span class="float-right"><?= $row['totalUpvotes'] ?> contribution upvotes</span></p>
                    </div>
                <?php endforeach ?>
            <?php else : ?>
                <?php foreach ($results as $row): ?>
                    <div class='container border'>
                        <h2><a href='post.php?postID=<?= $row['postID'] ?>'><?= $row['title'] ?></a></h2>
                        <p>Posted by <b><?= $row['username'] ?? "[deleted]" ?></b> on <?= $row['postDate'] ?>
                            <span class="float-right"><?= $row['upvotes'] ?> Upvotes</span>
                        </p>
                    </div>
                <?php endforeach; ?>
                <?php if ($totalPages > 1): ?>
                    <form method="get" class="pagination-form">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="btn btn-secondary">Previous</a>
                        <?php endif; ?>

                        Page <input type="number" name="page" value="<?= $page ?>" min="1" max="<?= $totalPages ?>" style="width:60px; text-align:center;">
                        of <?= $totalPages ?>
                        <button type="submit" class="btn btn-primary">Go</button>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="btn btn-secondary">Next</a>
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

-->

<template>
    <!-- Sticky add post button -->
    <Link href="/forum/post/create" form="searchForm" style="position: fixed; bottom: 60px; right: 30px; z-index: 100;">
        <Button rounded size="xlarge">
            <template #icon>
                <Plus />
            </template>
        </Button>
    </Link>

    <div>
        <h1 class="text-4xl font-bold m-4">Forums</h1>
        <Card>
            <template #content>
                <form @submit.prevent="searchForm.get('/forum')">
                    <div class="flex w-full gap-2">
                        <InputText class="w-full" v-model="searchForm.search" placeholder="Search keywords..." />
                        <Button label="Search" />
                    </div>
                    <div class="flex w-full gap-4 py-4">
                        <div>
                            <label for="searchType">Search: </label>
                            <Select v-model="searchForm.searchType" :options="[{ label: 'Post', value: 'post' }, { label: 'User', value: 'user' }]"
                                optionLabel="label" optionValue="value" />
                        </div>
                        <div>
                        <label for="sortType">Sort by: </label>
                        <Select v-model="searchForm.sortType"
                            :options="[{ label: 'Latest', value: 'latest' }, { label: 'Alphabet', value: 'alphabet' }, { label: 'Most Upvotes', value: 'upvote' }]"
                            optionLabel="label" optionValue="value" />
                    </div>
                    </div>
                </form>
            </template>
        </Card>
    </div>

    <div style='margin-bottom: 100px;'>
        <Deferred data="posts">
            <template #fallback>
                <ProgressSpinner />
            </template>

            <template #default>
                <template v-if="posts.data.length == 0">
                    <p class="flex justify-center items-center">No posts found</p>
                </template>

                <template v-else>
                    <template v-for="post in posts.data" :key="post.post_id">
                        <Link :href="'/forum/post/' + post.post_id">
                            <Card class="my-2">
                                <template #title>
                                    <h2 class="text-xl font-bold">{{ post.title }}</h2>
                                </template>
                                <template #content>
                                    <p class="mb-2">{{ post.description.substring(0, 100) + "..." }}</p>
                                </template>
                                <template #footer>
                                    <div class="flex flex-wrap justify-between">
                                        <p>{{ post.username ?? "[deleted]" }} on {{ post.created_at }}</p>
                                        <p>{{ post.upvotes }} Upvotes</p>
                                    </div>
                                </template>
                            </Card>
                        </Link>
                    </template>
                </template>
            </template>
        </Deferred>
    </div>
</template>

<script setup>
import { Link, Deferred, useForm } from '@inertiajs/vue3'

import Button from 'primevue/button'
import Card from 'primevue/card'
import InputText from 'primevue/inputtext';
import ProgressSpinner from 'primevue/progressspinner';
import Select from 'primevue/select';

import Plus from '@primeicons/vue/plus';

const searchForm = useForm({
  search: '',
  searchType: 'post',
  sortType: 'latest',
})

const props = defineProps({ posts: Object })
</script>
<div id="edit-profile-form" class="edit-profile-form" style="display: none;">
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" placeholder="Name" required>
        <textarea name="bio" placeholder="About Me" required><?php echo htmlspecialchars($user['bio']); ?></textarea>

        <!-- Add the location input field -->
        <input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" placeholder="<please enter a location>" required>

        <label for="skills">Skills (check all that apply):</label>
        <div class="checkbox-group">
            <?php
            $allSkills = ["Communication", "Teamwork", "Problem-Solving", "Leadership", "Technical Skills", "Time Management"];
            foreach ($allSkills as $skill):
                $checked = in_array($skill, $user['skills']) ? 'checked' : '';
            ?>
                <label>
                    <input type="checkbox" name="skills[]" value="<?php echo $skill; ?>" <?php echo $checked; ?>> <?php echo $skill; ?>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Check if avatar exists -->
        <?php if (isset($row['avatar']) && !empty($row['avatar'])): ?>
            <img src="<?php echo "img/avatar/" . htmlspecialchars($row['avatar']); ?>" alt="User Avatar">
        <?php else: ?>
            <img src="img/default-avatar.png" alt="Default User Avatar">
        <?php endif; ?>

        <form action="inc/uploadAvatar.php" method="POST" enctype="multipart/form-data">
            <input type="file" name="avatar" accept="image/*">
            <button type="submit" name="upload">Save Changes</button>
        </form>
    </form>
</div>

